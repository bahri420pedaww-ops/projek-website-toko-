<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'label', 'receiver_name', 'receiver_phone',
        'province', 'city', 'district', 'postal_code',
        'street_address', 'latitude', 'longitude', 'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'latitude'   => 'float',
        'longitude'  => 'float',
    ];

    /** Alamat satu baris: jalan, kecamatan, kota, provinsi, kode pos. */
    public function getFullAddressAttribute(): string
    {
        return collect([
            $this->street_address,
            $this->district,
            $this->city,
            $this->province,
            $this->postal_code,
        ])->filter()->implode(', ');
    }

    /** Tautan ke peta OpenStreetMap, atau null kalau pembeli tidak menandai lokasi. */
    public function getMapUrlAttribute(): ?string
    {
        if ($this->latitude === null || $this->longitude === null) {
            return null;
        }

        return "https://www.openstreetmap.org/?mlat={$this->latitude}&mlon={$this->longitude}"
            . "#map=17/{$this->latitude}/{$this->longitude}";
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
