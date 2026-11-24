<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dipicu SETELAH pesanan berstatus "shipped" (transaksi database sudah selesai).
 * Langkah Email Notifikasi nanti cukup memasang listener untuk event ini.
 */
class OrderShipped
{
    use Dispatchable, SerializesModels;

    public function __construct(public Order $order)
    {
    }
}
