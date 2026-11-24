<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Throwable;

/**
 * Alat uji lokal: meniru notifikasi Midtrans (dengan signature yang benar) tanpa perlu
 * tunnel atau internet. Hanya berjalan di mode sandbox.
 *
 * Contoh:
 *   php artisan payments:simulate INV-20261123-AB12CD settlement
 *   php artisan payments:simulate INV-20261123-AB12CD expire
 */
class SimulatePaymentNotification extends Command
{
    protected $signature = 'payments:simulate {order_id : order_id Midtrans (kolom payments.gateway_order_id)}
                            {status=settlement : settlement|pending|expire|cancel|deny}';

    protected $description = 'Simulasikan notifikasi pembayaran Midtrans (hanya untuk sandbox/uji lokal)';

    public function handle(PaymentService $payments): int
    {
        if (config('midtrans.is_production')) {
            $this->error('Perintah ini dinonaktifkan di mode production.');

            return self::FAILURE;
        }

        $orderId = (string) $this->argument('order_id');
        $status = (string) $this->argument('status');

        $payment = Payment::where('gateway_order_id', $orderId)->first();

        if (! $payment) {
            $this->error("Pembayaran dengan order_id {$orderId} tidak ditemukan. Buka halaman bayar pesanannya dulu.");

            return self::FAILURE;
        }

        $statusCodes = [
            'settlement' => '200',
            'pending'    => '201',
            'deny'       => '202',
            'expire'     => '407',
            'cancel'     => '200',
        ];

        if (! isset($statusCodes[$status])) {
            $this->error('Status harus salah satu dari: ' . implode(', ', array_keys($statusCodes)));

            return self::FAILURE;
        }

        $payload = [
            'order_id'           => $orderId,
            'status_code'        => $statusCodes[$status],
            'gross_amount'       => number_format($payment->gross_amount, 2, '.', ''),
            'transaction_status' => $status,
            'fraud_status'       => 'accept',
            'payment_type'       => 'bank_transfer',
            'transaction_id'     => (string) Str::uuid(),
            'settlement_time'    => now('Asia/Jakarta')->format('Y-m-d H:i:s'),
        ];

        $payload['signature_key'] = hash(
            'sha512',
            $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . config('midtrans.server_key')
        );

        try {
            $result = $payments->handleNotification($payload);
        } catch (Throwable $e) {
            $this->error('Gagal: ' . $e->getMessage());

            return self::FAILURE;
        }

        $this->info("Notifikasi '{$status}' diproses. Status pembayaran sekarang: " . ($result?->status ?? '-'));

        return self::SUCCESS;
    }
}
