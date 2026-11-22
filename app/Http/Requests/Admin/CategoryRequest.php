<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Akses sudah dibatasi oleh middleware 'admin' di routes/admin.php
        return true;
    }

    public function rules(): array
    {
        $category = $this->route('category');

        $parentRules = [
            'nullable',
            'integer',
            Rule::exists('categories', 'id')->whereNull('deleted_at'),
        ];

        // Kategori tidak boleh menjadi induk dirinya sendiri
        if ($category) {
            $parentRules[] = Rule::notIn([$category->id]);
        }

        return [
            'name'      => ['required', 'string', 'max:255'],
            'parent_id' => $parentRules,
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name'      => 'nama kategori',
            'parent_id' => 'kategori induk',
        ];
    }
}
