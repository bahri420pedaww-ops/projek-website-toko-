<?php

namespace App\Notifications\Concerns;

use App\Models\Order;
use Illuminate\Notifications\Messages\MailMessage;

trait DescribesOrder
{
    protected function rupiah(int|float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    protected function shopName(): string
    {
        return (string) config('shop.name');
    }

    /** Tambahkan rincian barang dan biaya ke email. */
    protected function addOrderSummary(MailMessage $mail, Order $order): MailMessage
    {
        $order->loadMissing('items');

        $mail->line('**Rincian pesanan**');

        foreach ($order->items as $item) {
            $mail->line("- {$item->product_name} × {$item->quantity}: {$this->rupiah($item->subtotal)}");
        }

        $mail->line("Subtotal: {$this->rupiah($order->subtotal)}");
        $mail->line("Ongkos kirim: {$this->rupiah($order->shipping_cost)}");

        if ($order->discount_total > 0) {
            $mail->line("Diskon: - {$this->rupiah($order->discount_total)}");
        }

        $mail->line("**Total: {$this->rupiah($order->grand_total)}**");

        return $mail;
    }

    /** Tambahkan alamat pengiriman ke email. */
    protected function addShippingInfo(MailMessage $mail, Order $order): MailMessage
    {
        $mail->line('**Dikirim ke**');
        $mail->line("{$order->receiver_name} ({$order->receiver_phone})");
        $mail->line(trim("{$order->shipping_address}, {$order->city_name}, {$order->province_name} {$order->postal_code}"));

        return $mail;
    }
}
