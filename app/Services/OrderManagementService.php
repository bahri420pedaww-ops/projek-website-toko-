<?php

namespace App\Services;

use App\Exceptions\MidtransException;
use App\Models\Order;
use App\Support\OrderStatus;
use DomainException;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Pembatalan pesanan oleh pembeli atau admin, dengan pengaman terhadap pembayaran
 * yang sebenarnya sudah masuk tetapi belum tercatat (webhook tidak sampai).
 */
class OrderManagementService
{
    public function __construct(
        private OrderService $orders,
        private PaymentService $payments,
        private MidtransClient $midtrans,
    ) {
    }

    /**
     * Batalkan pesanan yang belum dibayar.
     *
     * Urutan: (1) tanya status ke Midtrans supaya pesanan yang ternyata sudah dibayar tidak
     * terbatalkan, (2) batalkan pesanan dan kembalikan stok, (3) batalkan transaksi yang masih
     * menggantung di Midtrans agar nomor VA tidak bisa dipakai lagi.
     *
     * @throws DomainException
     */
    public function cancelUnpaid(Order $order, string $reason, ?int $changedBy = null): void
    {
        if (config('midtrans.server_key')) {
            try {
                $this->payments->syncLatest($order);
            } catch (MidtransException $e) {
                throw new DomainException(
                    'Status pembayaran belum bisa diperiksa ke Midtrans, jadi pesanan belum dibatalkan. '
                    . 'Coba lagi sebentar lagi.'
                );
            }
        }

        $order->refresh();

        if ($order->status !== Order::PENDING) {
            throw new DomainException(
                'Pesanan tidak bisa dibatalkan karena statusnya sekarang "'
                . OrderStatus::label($order->status) . '".'
            );
        }

        $this->orders->cancel($order, $reason, $changedBy);

        foreach ($order->payments()->where('status', 'pending')->get() as $payment) {
            $payment->update(['status' => 'cancel']);

            try {
                $this->midtrans->cancel($payment->gateway_order_id);
            } catch (Throwable $e) {
                // Tidak fatal: pesanan sudah dibatalkan di sisi toko
                Log::warning("Gagal membatalkan transaksi Midtrans {$payment->gateway_order_id}: " . $e->getMessage());
            }
        }
    }
}
