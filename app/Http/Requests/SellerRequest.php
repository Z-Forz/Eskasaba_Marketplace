<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellerRequest extends FormRequest
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

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'status' => [
                'nullable',
                'in:pending,approved,rejected',
            ],

        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [

            'description.string' => 'Deskripsi toko harus berupa teks.',
            'description.max'    => 'Deskripsi toko maksimal 1000 karakter.',

            'status.in'          => 'Status penjual tidak valid.',

        ];
    }
}