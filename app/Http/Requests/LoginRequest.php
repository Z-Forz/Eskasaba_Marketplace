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
        if (! $this->has('nis_nip')) {
            $input = $this->input('email') ?? $this->input('nis') ?? $this->input('nip') ?? $this->input('username');
            if ($input) {
                $this->merge([
                    'nis_nip' => $input,
                ]);
            }
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
            'nis_nip.required'  => 'NIS/NIP wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ];
    }
}
