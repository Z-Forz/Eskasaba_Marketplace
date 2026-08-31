<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        return [
            'username'       => ['required', 'string', 'max:255'],
            'nis_nip'        => ['nullable', 'string', 'max:255', 'unique:users,nis_nip,' . $userId],
            'email'          => ['nullable', 'email', 'max:255', 'unique:users,email,' . $userId],
            'password'       => [$this->isMethod('POST') ? 'required' : 'nullable', 'string', 'min:6'],
            'role'           => ['required', 'in:student,teacher'],
            'class_room'     => ['nullable', 'string', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:255'],
        ];
    }
}
