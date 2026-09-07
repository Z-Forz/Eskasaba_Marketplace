<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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

            'name' => [
                'required',
                'string',
                'max:255',
                'unique:categories,name,' . $this->category?->id,
            ],

            'icon' => [
                'nullable',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [

            'name.required' => 'Nama kategori wajib diisi.',
            'name.string'   => 'Nama kategori harus berupa teks.',
            'name.max'      => 'Nama kategori maksimal 255 karakter.',
            'name.unique'   => 'Nama kategori sudah digunakan.',

            'icon.string'   => 'Icon harus berupa teks class FontAwesome.',
            'icon.max'      => 'Icon maksimal 255 karakter.',

            'description.string' => 'Deskripsi harus berupa teks.',

        ];
    }
}