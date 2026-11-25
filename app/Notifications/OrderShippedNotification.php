<?php

namespace App\Notifications;

use App\Models\Order;
use App\Notifications\Concerns\DescribesOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/** Email ke pembeli: pesanan dikirim, lengkap dengan nomor resi. */
class OrderShippedNotification extends Notification implements ShouldQueue
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
        $order = $this->order->loadMissing(['user', 'shippingMethod', 'shipment']);

        $mail = (new MailMessage)
            ->subject("Pesananmu {$order->order_code} sedang dikirim")
            ->greeting('Halo ' . ($order->user->name ?? $order->receiver_name) . '!')
            ->line("Kabar baik! Pesanan **{$order->order_code}** sudah kami serahkan ke kurir.");

        $courier = trim(($order->shippingMethod->courier ?? '') . ' ' . ($order->shippingMethod->service ?? ''));

        if ($courier !== '') {
            $mail->line("Kurir: **{$courier}**");
        }

        if ($order->shipment?->tracking_number) {
            $mail->line("Nomor resi: **{$order->shipment->tracking_number}**");
        }

        if ($order->shipping_etd) {
            $mail->line("Estimasi tiba: {$order->shipping_etd}");
        }

        $this->addShippingInfo($mail, $order);

        return $mail
            ->line('Setelah barang kamu terima, mohon konfirmasi lewat halaman pesanan.')
            ->action('Lihat Pesanan', route('orders.show', $order->order_code))
            ->salutation("Salam, {$this->shopName()}");
    }
}
