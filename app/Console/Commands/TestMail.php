<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Menguji pengaturan email (.env) dengan mengirim satu email sederhana LANGSUNG
 * (tanpa antrean), sehingga kesalahan pengaturan langsung terlihat.
 *
 *   php artisan shop:test-mail emailkamu@gmail.com
 */
class TestMail extends Command
{
    protected $signature = 'shop:test-mail {email : Alamat tujuan}';

    protected $description = 'Kirim email uji untuk memeriksa pengaturan email di .env';

    public function handle(): int
    {
        $to = (string) $this->argument('email');
        $mailer = (string) config('mail.default');

        $this->line("Mailer: {$mailer} | Dari: " . config('mail.from.address') . " | Antrean: " . config('queue.default'));

        try {
            Mail::raw(
                'Ini email uji dari ' . config('shop.name') . '. Kalau kamu membacanya, pengiriman email sudah berfungsi.',
                fn ($message) => $message->to($to)->subject('Uji email ' . config('shop.name'))
            );
        } catch (Throwable $e) {
            $this->error('Gagal mengirim: ' . $e->getMessage());

            return self::FAILURE;
        }

        if ($mailer === 'log') {
            $this->info('Email ditulis ke storage/logs/laravel.log (MAIL_MAILER=log), belum dikirim ke kotak masuk.');
        } else {
            $this->info("Email uji dikirim ke {$to}. Periksa kotak masuk dan folder Spam.");
        }

        return self::SUCCESS;
    }
}
