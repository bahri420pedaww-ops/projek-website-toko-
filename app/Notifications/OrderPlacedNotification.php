<?php

namespace App\Notifications;

use App\Models\Order;
use App\Notifications\Concerns\DescribesOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Email ke pembeli: pesanan berhasil dibuat, menunggu pembayaran. */
class OrderPlacedNotification extends Notification implements ShouldQueue
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
        $hours = (int) config('midtrans.expiry_hours', 24);

        $mail = (new MailMessage)
            ->subject("Pesanan {$order->order_code} berhasil dibuat")
            ->greeting('Halo ' . ($order->user->name ?? $order->receiver_name) . '!')
            ->line("Terima kasih sudah berbelanja di {$this->shopName()}. Pesananmu sudah kami terima dan sedang menunggu pembayaran.")
            ->line("Kode pesanan: **{$order->order_code}**");

        $this->addOrderSummary($mail, $order);
        $this->addShippingInfo($mail, $order);

        return $mail
            ->line("Selesaikan pembayaran dalam {$hours} jam. Pesanan yang tidak dibayar akan dibatalkan otomatis.")
            ->action('Lihat dan Bayar Pesanan', route('orders.show', $order->order_code))
            ->salutation("Salam, {$this->shopName()}");
    }
}
