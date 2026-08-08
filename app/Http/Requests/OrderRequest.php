<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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

            'seller_id' => [
                'required',
                'exists:sellers,id',
            ],

            'total_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'status' => [
                'nullable',
                'in:pending,paid,processing,completed,cancelled',
            ],

        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [

            'seller_id.required' => 'Penjual wajib dipilih.',
            'seller_id.exists'   => 'Penjual tidak ditemukan.',

            'total_price.required' => 'Total harga wajib diisi.',
            'total_price.numeric'  => 'Total harga harus berupa angka.',
            'total_price.min'      => 'Total harga tidak boleh kurang dari 0.',

            'status.in' => 'Status pesanan tidak valid.',

        ];
    }
}