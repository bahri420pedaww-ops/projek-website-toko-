<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dipicu SETELAH pesanan berstatus "paid" (transaksi database sudah selesai).
 * Langkah Email Notifikasi nanti cukup memasang listener untuk event ini.
 */
class OrderPaid
{
    use Dispatchable, SerializesModels;

    public function __construct(public Order $order)
    {
    }
}
