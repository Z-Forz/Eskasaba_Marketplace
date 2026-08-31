<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('price')) {
            $rawPrice = preg_replace('/[^\d.]/', '', str_replace('.', '', (string) $this->price));
            $this->merge([
                'price' => $rawPrice !== '' ? $rawPrice : null,
            ]);
        }

        if ($this->has('discount')) {
            $rawDiscount = preg_replace('/[^\d.]/', '', str_replace('.', '', (string) $this->discount));
            $this->merge([
                'discount' => $rawDiscount !== '' ? $rawDiscount : 0,
            ]);
        }

        if ($this->has('variants') && is_array($this->variants)) {
            $cleanedVariants = [];
            foreach ($this->variants as $variant) {
                if (is_array($variant) && !empty($variant['name'])) {
                    $rawVarPrice = isset($variant['price']) ? preg_replace('/[^\d.]/', '', str_replace('.', '', (string) $variant['price'])) : 0;
                    $cleanedVariants[] = [
                        'name'  => trim($variant['name']),
                        'price' => (float) ($rawVarPrice !== '' ? $rawVarPrice : 0),
                    ];
                }
            }
            $this->merge([
                'variants' => !empty($cleanedVariants) ? $cleanedVariants : null,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [

            'category_id' => [
                'required',
                'exists:categories,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'stock' => [
                'required',
                'integer',
                'min:0',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'condition' => [
                'nullable',
                'string',
                'max:100',
            ],

            'status' => [
                'nullable',
                'in:active,inactive',
            ],
            'discount' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999.99',
            ],
            'variants' => [
                'nullable',
                'array',
            ],
            'variants.*.name' => [
                'required_with:variants',
                'string',
                'max:100',
            ],
            'variants.*.price' => [
                'required_with:variants',
                'numeric',
                'min:0',
            ],
            'images' => [
                'nullable',
                'array',
                'max:5',
            ],
            'images.*' => [
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:5120',
            ],
            'delete_images' => [
                'nullable',
                'array',
            ],
            'delete_images.*' => [
                'integer',
                'exists:product_images,id',
            ],

        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [

            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists'   => 'Kategori tidak ditemukan.',

            'name.required' => 'Nama produk wajib diisi.',
            'name.string'   => 'Nama produk harus berupa teks.',
            'name.max'      => 'Nama produk maksimal 255 karakter.',

            'price.required' => 'Harga produk wajib diisi.',
            'price.numeric'  => 'Harga produk harus berupa angka.',
            'price.min'      => 'Harga produk tidak boleh kurang dari 0.',

            'stock.required' => 'Stok produk wajib diisi.',
            'stock.integer'  => 'Stok produk harus berupa angka.',
            'stock.min'      => 'Stok produk tidak boleh kurang dari 0.',

            'description.string' => 'Deskripsi harus berupa teks.',
            'condition.string'   => 'Kondisi/varian harus berupa teks.',
            'condition.max'      => 'Kondisi/varian maksimal 100 karakter.',

            'status.in' => 'Status produk tidak valid.',

            'images.max'      => 'Maksimal foto produk yang dapat diunggah adalah 5 foto.',
            'images.*.image'  => 'File yang diunggah harus berupa gambar.',
            'images.*.mimes'  => 'Format gambar harus jpeg, png, jpg, atau webp.',
            'images.*.max'    => 'Ukuran masing-masing foto maksimal 5 MB.',

        ];
    }
}