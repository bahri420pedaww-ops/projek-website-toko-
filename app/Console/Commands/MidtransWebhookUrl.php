<?php

namespace App\Console\Commands;

use App\Services\MidtransClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Menampilkan alamat webhook yang akan dipakai untuk transaksi baru, dan (opsional)
 * menguji apakah alamat itu benar-benar sampai ke aplikasi ini.
 *
 *   php artisan midtrans:webhook-url
 *   php artisan midtrans:webhook-url --ping
 */
class MidtransWebhookUrl extends Command
{
    protected $signature = 'midtrans:webhook-url {--ping : Kirim permintaan uji ke alamat webhook}';

    protected $description = 'Tampilkan alamat webhook Midtrans untuk transaksi baru dan uji jangkauannya';

    public function handle(MidtransClient $midtrans): int
    {
        if (config('midtrans.is_production')) {
            $this->warn('Mode production: alamat webhook diatur di dashboard Midtrans, bukan lewat .env.');

            return self::SUCCESS;
        }

        $setting = trim((string) config('midtrans.notification_url'));

        if ($setting === '') {
            $this->warn('MIDTRANS_NOTIFICATION_URL kosong di .env, jadi Midtrans memakai Notification URL dari dashboard.');
            $this->line('Isi MIDTRANS_NOTIFICATION_URL=ngrok lalu jalankan: php artisan config:clear');

            return self::SUCCESS;
        }

        $url = $midtrans->notificationUrl();

        if (! $url) {
            $this->error('Alamat ngrok tidak ditemukan. Pastikan ngrok sedang berjalan (ngrok http 8000).');

            return self::FAILURE;
        }

        $this->info('Alamat webhook untuk transaksi baru:');
        $this->line('  ' . $url);

        if (! $this->option('ping')) {
            $this->line('Tambahkan --ping untuk menguji apakah alamat ini sampai ke aplikasi.');

            return self::SUCCESS;
        }

        // Kirim notifikasi PALSU (signature salah). Aplikasi yang benar menjawab 403;
        // itu membuktikan tunnel, aplikasi, dan pemeriksaan signature semuanya bekerja.
        try {
            $response = Http::timeout(15)
                ->withHeaders(['ngrok-skip-browser-warning' => '1'])
                ->acceptJson()
                ->asJson()
                ->post($url, [
                    'order_id'      => 'uji-koneksi',
                    'status_code'   => '200',
                    'gross_amount'  => '1.00',
                    'signature_key' => 'sengaja-salah',
                ]);
        } catch (Throwable $e) {
            $this->error('Gagal menghubungi alamat itu: ' . $e->getMessage());

            return self::FAILURE;
        }

        if ($response->status() === 403) {
            $this->info('BERHASIL: alamat sampai ke aplikasi dan notifikasi palsu ditolak (403) sebagaimana mestinya.');

            return self::SUCCESS;
        }

        $this->error('Respons tidak terduga: HTTP ' . $response->status() . '. Pastikan "php artisan serve --port=8000" berjalan dan ngrok mengarah ke port 8000.');

        return self::FAILURE;
    }
}
