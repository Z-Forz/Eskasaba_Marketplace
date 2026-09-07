<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartRequest extends FormRequest
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

            'product_id' => [
                'required',
                'exists:products,id',
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],

        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [

            'product_id.required' => 'Produk wajib dipilih.',
            'product_id.exists'   => 'Produk tidak ditemukan.',

            'quantity.required'   => 'Jumlah barang wajib diisi.',
            'quantity.integer'    => 'Jumlah barang harus berupa angka.',
            'quantity.min'        => 'Jumlah barang minimal 1.',

        ];
    }
}