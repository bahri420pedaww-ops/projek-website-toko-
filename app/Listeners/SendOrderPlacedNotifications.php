<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderPlacedNotification;
use App\Support\AdminRecipients;
use Throwable;

/**
 * Pesanan dibuat: email ke pembeli dan ke admin.
 * Kegagalan email TIDAK boleh menggagalkan checkout, jadi dibungkus try/catch.
 */
class SendOrderPlacedNotifications
{
    public function handle(OrderPlaced $event): void
    {
        try {
            $order = $event->order->loadMissing('user');

            $order->user?->notify(new OrderPlacedNotification($order));

            AdminRecipients::notify(new NewOrderNotification($order));
        } catch (Throwable $e) {
            report($e);
        }
    }
}
