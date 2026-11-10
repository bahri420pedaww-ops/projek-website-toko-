<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    public const PENDING    = 'pending';
    public const PAID       = 'paid';
    public const PROCESSING = 'processing';
    public const SHIPPED    = 'shipped';
    public const COMPLETED  = 'completed';
    public const CANCELLED  = 'cancelled';

    protected $fillable = [
        'order_code', 'user_id', 'shipping_method_id',
        'receiver_name', 'receiver_phone', 'shipping_address',
        'city_name', 'province_name', 'postal_code',
        'subtotal', 'shipping_cost', 'discount_total', 'grand_total',
        'status', 'note',
    ];

    /** Contoh hasil: INV-20261110-AB12CD */
    public static function generateCode(): string
    {
        do {
            $code = 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        } while (static::where('order_code', $code)->exists());

        return $code;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function histories()
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }
}
