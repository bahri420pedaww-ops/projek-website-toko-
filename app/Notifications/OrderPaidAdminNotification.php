<?php

namespace App\Notifications;

use App\Models\Order;
use App\Notifications\Concerns\DescribesOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Email ke admin: pesanan sudah dibayar dan perlu diproses. */
class OrderPaidAdminNotification extends Notification implements ShouldQueue
{
    use Queueable, DescribesOrder;

    public int $tries = 3;

    public array $backoff = [30, 120];

    public function __construct(public Order $order)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $order = $this->order->loadMissing(['items', 'user']);

        $mail = (new MailMessage)
            ->subject("Pembayaran diterima: {$order->order_code}")
            ->greeting('Halo Admin!')
            ->line("Pesanan **{$order->order_code}** dari **" . ($order->user->name ?? $order->receiver_name) . "** sudah dibayar sebesar **{$this->rupiah($order->grand_total)}**.")
            ->line('Silakan proses dan kirim pesanan ini.');

        $this->addOrderSummary($mail, $order);
        $this->addShippingInfo($mail, $order);

        return $mail
            ->action('Proses Pesanan', route('admin.orders.show', $order))
            ->salutation("{$this->shopName()}");
    }
}
