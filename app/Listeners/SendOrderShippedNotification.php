<?php

namespace App\Listeners;

use App\Events\OrderShipped;
use App\Notifications\OrderShippedNotification;
use Throwable;

/**
 * Pesanan dikirim: email ke pembeli beserta nomor resi.
 */
class SendOrderShippedNotification
{
    public function handle(OrderShipped $event): void
    {
        try {
            $order = $event->order->loadMissing('user');

            $order->user?->notify(new OrderShippedNotification($order));
        } catch (Throwable $e) {
            report($e);
        }
    }
}
