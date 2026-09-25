<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $review = $this->route('review');

        $uniqueProductRule = Rule::unique('reviews', 'product_id')
            ->where(fn ($query) => $query->where('order_id', $this->order_id));

        if ($review) {
            $uniqueProductRule->ignore($review->id);
        }

        return [

            'order_id' => [
                'required',
                // pesanan harus milik user yang login DAN sudah completed
                Rule::exists('orders', 'id')->where(
                    fn ($query) => $query->where('user_id', auth()->id())
                        ->where('status', 'completed')
                ),
            ],

            'product_id' => [
                'required',
                'exists:products,id',
                // produk harus beneran ada di order_items pesanan ini
                Rule::exists('order_items', 'product_id')->where(
                    fn ($query) => $query->where('order_id', $this->order_id)
                ),
                // belum pernah direview di order yang sama (abaikan ID jika update)
                $uniqueProductRule,
            ],

            'rating' => [
                'required',
                'integer',
                'between:1,5',
            ],

            'comment' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:10240',
            ],

            'remove_image' => [
                'nullable',
                'boolean',
            ],

        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [

            'order_id.required' => 'Pesanan wajib diisi.',
            'order_id.exists'   => 'Pesanan tidak valid atau belum selesai.',

            'product_id.required' => 'Produk wajib dipilih.',
            'product_id.exists'   => 'Produk tidak ditemukan atau bukan bagian dari pesanan ini.',
            'product_id.unique'   => 'Produk ini sudah pernah Anda review.',

            'rating.required' => 'Rating wajib diisi.',
            'rating.integer'  => 'Rating harus berupa angka.',
            'rating.between'  => 'Rating harus bernilai 1 sampai 5.',

            'comment.string' => 'Komentar harus berupa teks.',
            'comment.max'    => 'Komentar maksimal 1000 karakter.',

            'image.image' => 'File ulasan harus berupa gambar.',
            'image.mimes' => 'Format gambar ulasan yang diperbolehkan: jpeg, png, jpg, webp.',
            'image.max'   => 'Ukuran gambar maksimal 10MB.',

        ];
    }
}