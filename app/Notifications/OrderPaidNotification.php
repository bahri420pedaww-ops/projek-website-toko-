<?php

namespace App\Notifications;

use App\Models\Order;
use App\Notifications\Concerns\DescribesOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Email ke pembeli: pembayaran diterima. */
class OrderPaidNotification extends Notification implements ShouldQueue
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
            ->subject("Pembayaran pesanan {$order->order_code} diterima")
            ->greeting('Halo ' . ($order->user->name ?? $order->receiver_name) . '!')
            ->line("Pembayaran sebesar **{$this->rupiah($order->grand_total)}** untuk pesanan **{$order->order_code}** sudah kami terima.")
            ->line('Pesananmu akan segera kami proses dan kirim. Kami akan memberi kabar lagi saat pesanan dikirim.');

        $this->addOrderSummary($mail, $order);

        return $mail
            ->action('Lihat Pesanan', route('orders.show', $order->order_code))
            ->salutation("Salam, {$this->shopName()}");
    }
}
