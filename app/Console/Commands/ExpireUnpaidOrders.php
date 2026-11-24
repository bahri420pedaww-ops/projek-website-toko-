<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Console\Command;
use Throwable;

class ExpireUnpaidOrders extends Command
{
    protected $signature = 'orders:expire-unpaid';

    protected $description = 'Batalkan pesanan yang belum dibayar melewati batas waktu, lalu kembalikan stoknya';

    public function handle(PaymentService $payments, OrderService $orders): int
    {
        $limit = now()->subHours((int) config('midtrans.expiry_hours'));

        $cancelled = 0;
        $skipped = 0;

        $candidates = Order::where('status', Order::PENDING)
            ->where('created_at', '<', $limit)
            ->orderBy('id')
            ->get();

        foreach ($candidates as $order) {
            try {
                // Periksa dulu ke Midtrans: siapa tahu sudah dibayar tetapi webhook tidak sampai
                if (config('midtrans.server_key')) {
                    $payments->syncLatest($order);
                }

                $order->refresh();

                if ($order->status !== Order::PENDING) {
                    continue; // sudah dibayar, atau sudah dibatalkan oleh sinkronisasi tadi
                }

                $orders->cancel($order, 'Batas waktu pembayaran habis.');
                $cancelled++;
            } catch (Throwable $e) {
                // Kalau status tidak bisa diverifikasi, pesanan TIDAK dibatalkan
                $skipped++;
                $this->warn("Pesanan {$order->order_code} dilewati: " . $e->getMessage());
            }
        }

        $this->info("Selesai. Dibatalkan: {$cancelled}, dilewati: {$skipped}.");

        return self::SUCCESS;
    }
}
