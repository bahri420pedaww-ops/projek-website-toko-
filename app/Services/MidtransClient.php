<?php

namespace App\Services;

use App\Exceptions\MidtransException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * Pembungkus tipis untuk API Midtrans memakai HTTP client bawaan Laravel
 * (tanpa paket tambahan). Autentikasi: Basic Auth dengan Server Key sebagai username.
 */
class MidtransClient
{
    /**
     * Minta token Snap untuk satu transaksi.
     *
     * @return array{token:string, redirect_url?:string}
     * @throws MidtransException
     */
    public function createSnapTransaction(array $payload): array
    {
        try {
            $response = $this->http()->asJson()->post((string) config('midtrans.snap_url'), $payload);
        } catch (ConnectionException $e) {
            throw new MidtransException($this->connectionMessage($e), 0, $e);
        }

        if ($response->status() === 401) {
            throw new MidtransException('Server Key Midtrans tidak valid (401). Periksa MIDTRANS_SERVER_KEY di .env.');
        }

        if ($response->failed()) {
            $errors = $response->json('error_messages');
            $message = is_array($errors) && $errors !== []
                ? implode(' ', $errors)
                : ($response->json('status_message') ?: 'Midtrans menolak permintaan pembayaran.');

            throw new MidtransException($message);
        }

        $data = $response->json();

        if (! is_array($data) || empty($data['token'])) {
            throw new MidtransException('Respon Midtrans tidak berisi token pembayaran.');
        }

        return $data;
    }

    /**
     * Ambil status transaksi dari Midtrans (Status API).
     * Mengembalikan null kalau transaksinya belum ada di Midtrans
     * (misalnya pembeli belum memilih metode pembayaran di popup).
     *
     * @throws MidtransException
     */
    public function status(string $orderId): ?array
    {
        $url = rtrim((string) config('midtrans.api_url'), '/') . '/v2/' . rawurlencode($orderId) . '/status';

        try {
            $response = $this->http()->get($url);
        } catch (ConnectionException $e) {
            throw new MidtransException($this->connectionMessage($e), 0, $e);
        }

        if ($response->status() === 401) {
            throw new MidtransException('Server Key Midtrans tidak valid (401). Periksa MIDTRANS_SERVER_KEY di .env.');
        }

        $data = $response->json();

        if (! is_array($data)) {
            throw new MidtransException('Respon status dari Midtrans tidak dapat dibaca.');
        }

        if ($response->status() === 404 || (string) ($data['status_code'] ?? '') === '404') {
            return null;
        }

        return $data;
    }

    private function http(): PendingRequest
    {
        $serverKey = (string) config('midtrans.server_key');

        if ($serverKey === '') {
            throw new MidtransException('MIDTRANS_SERVER_KEY belum diatur di file .env.');
        }

        return Http::withBasicAuth($serverKey, '')
            ->acceptJson()
            ->timeout(20)
            ->withOptions(['verify' => (bool) config('midtrans.verify_ssl', true)]);
    }

    private function connectionMessage(ConnectionException $e): string
    {
        if (str_contains($e->getMessage(), 'cURL error 60')) {
            return 'Koneksi aman ke Midtrans gagal (sertifikat SSL tidak dikenali oleh PHP di komputer ini). '
                 . 'Lihat panduan CARA_PAKAI bagian "cURL error 60".';
        }

        return 'Tidak dapat terhubung ke Midtrans. Periksa koneksi internet.';
    }
}
