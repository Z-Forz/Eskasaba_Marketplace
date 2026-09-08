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
        if ($this->has('email') && !$this->has('nis_nip')) {
            $this->merge([
                'nis_nip' => $this->input('email'),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'nis_nip'  => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'nis_nip.required'  => 'NIS / NIP / Email wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ];
    }
}
