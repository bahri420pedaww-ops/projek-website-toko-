<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id', 'gateway_order_id', 'transaction_id', 'payment_type',
        'gross_amount', 'status', 'snap_token', 'paid_at',
    ];

    protected $casts = ['paid_at' => 'datetime'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
