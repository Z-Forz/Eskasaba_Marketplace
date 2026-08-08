<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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

            'order_id' => [
                'required',
                'exists:orders,id',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'method' => [
                'required',
                'in:cod,qris',
            ],

            'proof' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],

            'status' => [
                'nullable',
                'in:pending,verified,rejected',
            ],

        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [

            'order_id.required' => 'Pesanan wajib dipilih.',
            'order_id.exists'   => 'Pesanan tidak ditemukan.',

            'amount.required'   => 'Jumlah pembayaran wajib diisi.',
            'amount.numeric'    => 'Jumlah pembayaran harus berupa angka.',

            'method.required'   => 'Metode pembayaran wajib dipilih.',
            'method.in'         => 'Metode pembayaran tidak valid.',

            'proof.image'       => 'Bukti pembayaran harus berupa gambar.',
            'proof.mimes'       => 'Format gambar harus JPG, JPEG, atau PNG.',
            'proof.max'         => 'Ukuran gambar maksimal 2 MB.',

            'status.in'         => 'Status pembayaran tidak valid.',

        ];
    }
}