<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Notifications\OrderPaidAdminNotification;
use App\Notifications\OrderPaidNotification;
use App\Support\AdminRecipients;
use Throwable;

/**
 * Pembayaran diterima: email ke pembeli dan ke admin.
 */
class SendOrderPaidNotifications
{
    public function handle(OrderPaid $event): void
    {
        try {
            $order = $event->order->loadMissing('user');

            $order->user?->notify(new OrderPaidNotification($order));

            AdminRecipients::notify(new OrderPaidAdminNotification($order));
        } catch (Throwable $e) {
            report($e);
        }
    }
}
