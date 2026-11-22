<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShippingMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Akses sudah dibatasi oleh middleware 'admin' di routes/admin.php
        return true;
    }

    public function rules(): array
    {
        $method = $this->route('method');

        return [
            'courier' => ['required', 'string', 'max:50'],
            'service' => [
                'required', 'string', 'max:50',
                Rule::unique('shipping_methods', 'service')
                    ->where('courier', $this->input('courier'))
                    ->ignore($method?->id),
            ],
            'is_active' => ['nullable', 'boolean'],

            // Tarif per zona. Dikosongkan = layanan ini tidak melayani zona tersebut.
            'rates'                  => ['required', 'array'],
            'rates.*.price_per_kg'   => ['nullable', 'integer', 'min:0', 'max:10000000'],
            'rates.*.estimated_days' => ['nullable', 'string', 'max:30'],
        ];
    }

    public function attributes(): array
    {
        return [
            'courier'                => 'nama kurir',
            'service'                => 'layanan',
            'rates.*.price_per_kg'   => 'tarif per kg',
            'rates.*.estimated_days' => 'estimasi hari',
        ];
    }

    public function messages(): array
    {
        return [
            'service.unique' => 'Layanan ini sudah ada untuk kurir tersebut.',
        ];
    }
}
