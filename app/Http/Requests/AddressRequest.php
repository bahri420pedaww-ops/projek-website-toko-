<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Route sudah dilindungi middleware 'auth'
        return true;
    }

    /** Rapikan nomor HP sebelum divalidasi: buang spasi, tanda hubung, titik, dan kurung. */
    protected function prepareForValidation(): void
    {
        if ($this->has('receiver_phone')) {
            $this->merge([
                'receiver_phone' => preg_replace('/[\s\-().]/', '', (string) $this->input('receiver_phone')),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'label'          => ['required', 'string', 'max:50'],
            'receiver_name'  => ['required', 'string', 'max:255'],
            'receiver_phone' => ['required', 'regex:/^(\+62|62|0)8[0-9]{8,12}$/'],
            'province'       => ['required', 'string', 'max:100'],
            'city'           => ['required', 'string', 'max:100'],
            'district'       => ['nullable', 'string', 'max:100'],
            'postal_code'    => ['nullable', 'regex:/^[0-9]{5}$/'],
            'street_address' => ['required', 'string', 'min:10', 'max:500'],
            'latitude'       => ['nullable', 'required_with:longitude', 'numeric', 'between:-90,90'],
            'longitude'      => ['nullable', 'required_with:latitude', 'numeric', 'between:-180,180'],
            'is_default'     => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'label'          => 'label alamat',
            'receiver_name'  => 'nama penerima',
            'receiver_phone' => 'nomor HP penerima',
            'province'       => 'provinsi',
            'city'           => 'kota/kabupaten',
            'district'       => 'kecamatan',
            'postal_code'    => 'kode pos',
            'street_address' => 'alamat lengkap',
            'latitude'       => 'titik lokasi',
            'longitude'      => 'titik lokasi',
        ];
    }

    public function messages(): array
    {
        return [
            'receiver_phone.regex' => 'Nomor HP tidak valid. Contoh: 081234567890.',
            'postal_code.regex'    => 'Kode pos harus 5 angka.',
            'street_address.min'   => 'Alamat lengkap terlalu pendek. Tulis jalan, nomor rumah, RT/RW, dan kelurahan.',
        ];
    }
}
