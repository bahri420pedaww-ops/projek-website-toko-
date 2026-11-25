<?php

namespace App\Notifications;

use App\Models\Order;
use App\Notifications\Concerns\DescribesOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Email ke admin: ada pesanan baru (belum dibayar). */
class NewOrderNotification extends Notification implements ShouldQueue
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
            ->subject("Pesanan baru {$order->order_code}")
            ->greeting('Halo Admin!')
            ->line("Ada pesanan baru dari **" . ($order->user->name ?? $order->receiver_name) . "** dengan total **{$this->rupiah($order->grand_total)}**.")
            ->line('Status: menunggu pembayaran.');

        $this->addOrderSummary($mail, $order);

        return $mail
            ->action('Buka Pesanan', route('admin.orders.show', $order))
            ->salutation("{$this->shopName()}");
    }
}
