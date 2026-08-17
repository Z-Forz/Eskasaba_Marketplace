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

            'condition' => [
                'nullable',
                'string',
                'max:100',
            ],

            'status' => [
                'nullable',
                'in:active,inactive',
            ],
            'discount' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
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
            'condition.string'   => 'Kondisi/varian harus berupa teks.',
            'condition.max'      => 'Kondisi/varian maksimal 100 karakter.',

            'status.in' => 'Status produk tidak valid.',

        ];
    }
}