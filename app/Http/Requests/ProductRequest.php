<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        return [

            'category_id' => [
                'required',
                'exists:categories,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'stock' => [
                'required',
                'integer',
                'min:0',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'status' => [
                'nullable',
                'in:active,inactive',
            ],

        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [

            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists'   => 'Kategori tidak ditemukan.',

            'name.required' => 'Nama produk wajib diisi.',
            'name.string'   => 'Nama produk harus berupa teks.',
            'name.max'      => 'Nama produk maksimal 255 karakter.',

            'price.required' => 'Harga produk wajib diisi.',
            'price.numeric'  => 'Harga produk harus berupa angka.',
            'price.min'      => 'Harga produk tidak boleh kurang dari 0.',

            'stock.required' => 'Stok produk wajib diisi.',
            'stock.integer'  => 'Stok produk harus berupa angka.',
            'stock.min'      => 'Stok produk tidak boleh kurang dari 0.',

            'description.string' => 'Deskripsi harus berupa teks.',

            'status.in' => 'Status produk tidak valid.',

        ];
    }
}