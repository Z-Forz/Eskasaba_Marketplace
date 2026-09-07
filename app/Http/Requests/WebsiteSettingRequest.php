<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WebsiteSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules.
     */
    public function rules(): array
    {
        return [
            'website_name' => [
                'required',
                'string',
                'max:255',
            ],

            'logo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp,svg',
                'max:2048',
            ],

            'hero_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],

            'hero_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'hero_description' => [
                'nullable',
                'string',
            ],

            'about' => [
                'nullable',
                'string',
            ],

            'vision' => [
                'nullable',
                'string',
            ],

            'mission' => [
                'nullable',
                'string',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'instagram' => [
                'nullable',
                'string',
                'max:255',
            ],

            'facebook' => [
                'nullable',
                'string',
                'max:255',
            ],

            'tiktok' => [
                'nullable',
                'string',
                'max:255',
            ],

            'copyright' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }
}