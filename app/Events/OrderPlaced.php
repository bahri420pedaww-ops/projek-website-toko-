<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dipicu SETELAH pesanan berhasil dibuat saat checkout (transaksi database sudah selesai).
 */
class OrderPlaced
{
    use Dispatchable, SerializesModels;

    public function __construct(public Order $order)
    {
    }
}
