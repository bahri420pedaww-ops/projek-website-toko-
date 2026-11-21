<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id'  => ['required', Rule::exists('categories', 'id')->whereNull('deleted_at')],
            'name'         => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'price'        => ['required', 'integer', 'min:0'],
            'stock'        => ['required', 'integer', 'min:0'],
            'weight_gram'  => ['required', 'integer', 'min:0'],
            'is_active'    => ['nullable', 'boolean'],
            'images'       => ['nullable', 'array', 'max:5'],
            'images.*'     => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'kategori',
            'name'        => 'nama produk',
            'description' => 'deskripsi',
            'price'       => 'harga',
            'stock'       => 'stok',
            'weight_gram' => 'berat',
            'images'      => 'gambar',
            'images.*'    => 'gambar',
        ];
    }
}
