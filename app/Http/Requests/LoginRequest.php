<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $input = $this->input('email') ?? $this->input('nis_nip') ?? $this->input('nis') ?? $this->input('nip') ?? $this->input('username');
        if ($input) {
            $this->merge([
                'email'   => $input,
                'nis_nip' => $input,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => 'Alamat email sekolah wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ];
    }
}
