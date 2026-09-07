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
            $str = trim((string) $this->price);
            if (preg_match('/\.00$/', $str)) {
                $str = substr($str, 0, -3);
            }
            $rawPrice = preg_replace('/[^\d]/', '', $str);
            $this->merge([
                'price' => $rawPrice !== '' ? $rawPrice : null,
            ]);
        }

        if ($this->has('discount')) {
            $str = trim((string) $this->discount);
            if (preg_match('/\.00$/', $str)) {
                $str = substr($str, 0, -3);
            }
            $rawDiscount = preg_replace('/[^\d]/', '', $str);
            $this->merge([
                'discount' => $rawDiscount !== '' ? $rawDiscount : 0,
            ]);
        }

        if ($this->has('variants') && is_array($this->variants)) {
            $cleanedVariants = [];
            foreach ($this->variants as $variant) {
                if (is_array($variant) && !empty($variant['name'])) {
                    $strV = isset($variant['price']) ? trim((string) $variant['price']) : '0';
                    if (preg_match('/\.00$/', $strV)) {
                        $strV = substr($strV, 0, -3);
                    }
                    $rawVarPrice = preg_replace('/[^\d]/', '', $strV);
                    $rawVarStock = isset($variant['stock']) ? (int) $variant['stock'] : 0;
                    $cleanedVariants[] = [
                        'name'  => trim($variant['name']),
                        'price' => (float) ($rawVarPrice !== '' ? $rawVarPrice : 0),
                        'stock' => max(0, $rawVarStock),
                    ];
                }
            }

            if (!empty($cleanedVariants)) {
                $totalStock = array_sum(array_column($cleanedVariants, 'stock'));
                $this->merge([
                    'variants' => $cleanedVariants,
                    'stock'    => $totalStock,
                ]);
            } else {
                $this->merge([
                    'variants' => null,
                ]);
            }
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
            'variants.*.stock' => [
                'required_with:variants',
                'integer',
                'min:0',
            ],
            'images' => [
                'nullable',
                'array',
                'max:5',
            ],
            'images.*' => [
                'file',
                'mimes:jpeg,png,jpg,webp,gif,bmp,jfif,heic,heif',
                'max:10240',
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