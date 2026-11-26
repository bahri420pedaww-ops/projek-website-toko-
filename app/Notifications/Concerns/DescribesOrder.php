<?php

namespace App\Notifications\Concerns;

use App\Models\Order;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

trait DescribesOrder
{
    /**
     * Pengaman: jangan kirim email ke alamat contoh atau fiktif (domain .test, example.com,
     * atau isian panduan seperti emailkamu@gmail.com). Email ke alamat seperti itu hanya
     * menghasilkan email pantulan ("Address not found") yang menumpuk di kotak masuk pengirim.
     * Metode ini dipanggil otomatis oleh Laravel sebelum email benar-benar dikirim.
     */
    public function shouldSend(object $notifiable, string $channel): bool
    {
        if ($channel !== 'mail') {
            return true;
        }

        $email = strtolower(trim((string) $notifiable->routeNotificationFor('mail', $this)));

        if ($email === '' || ! str_contains($email, '@')) {
            return false;
        }

        $domain = substr((string) strrchr($email, '@'), 1);

        $placeholderAddresses = ['emailkamu@gmail.com', 'emailtoko@gmail.com'];
        $reservedDomains = ['example.com', 'example.org', 'example.net', 'localhost'];

        $isFake = in_array($email, $placeholderAddresses, true)
            || in_array($domain, $reservedDomains, true)
            || preg_match('/\.(test|example|invalid|localhost)$/', $domain) === 1;

        if ($isFake) {
            Log::info("Email ke alamat contoh dilewati: {$email}. Ganti dengan alamat email nyata.");

            return false;
        }

        return true;
    }

    protected function rupiah(int|float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    protected function shopName(): string
    {
        return (string) config('shop.name');
    }

    /** Tambahkan rincian barang (tabel) dan biaya ke email. */
    protected function addOrderSummary(MailMessage $mail, Order $order): MailMessage
    {
        $order->loadMissing('items');

        $rows = [
            '| Produk | Jumlah | Subtotal |',
            '| :-- | :-: | --: |',
        ];

        foreach ($order->items as $item) {
            $name = str_replace('|', '\|', (string) $item->product_name);
            $rows[] = "| {$name} | {$item->quantity} | {$this->rupiah($item->subtotal)} |";
        }

        $mail->line('**Rincian pesanan**');
        $mail->line(implode("\n", $rows));

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
