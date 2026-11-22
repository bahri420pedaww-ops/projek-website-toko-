<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    // Kolom "cost" (tarif tetap) tidak dipakai lagi; ongkir kini dihitung dari tabel shipping_rates.
    protected $fillable = ['courier', 'service', 'cost', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function rates()
    {
        return $this->hasMany(ShippingRate::class)->orderBy('zone');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
