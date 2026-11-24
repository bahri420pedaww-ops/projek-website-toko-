<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * Perubahan status pesanan yang punya efek samping (riwayat status, stok).
 * Langkah Status Pesanan nanti memakai class ini juga (pembatalan oleh pembeli/admin).
 */
class OrderService
{
    /** Tandai pesanan sudah dibayar dan catat riwayatnya. */
    public function markPaid(Order $order, ?string $note = null, ?int $changedBy = null): void
    {
        $order->update(['status' => Order::PAID]);

        $order->histories()->create([
            'status'     => Order::PAID,
            'note'       => $note ?? 'Pembayaran diterima.',
            'changed_by' => $changedBy,
        ]);
    }

    /**
     * Batalkan pesanan yang BELUM dibayar dan kembalikan stoknya.
     * Pesanan yang sudah dibayar tidak bisa dibatalkan dari sini (fitur refund tidak dipakai).
     *
     * @throws DomainException
     */
    public function cancel(Order $order, string $reason, ?int $changedBy = null): void
    {
        DB::transaction(function () use ($order, $reason, $changedBy) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

            if ($order->status !== Order::PENDING) {
                throw new DomainException('Hanya pesanan yang belum dibayar yang bisa dibatalkan.');
            }

            // Kembalikan stok (produk yang sudah dihapus tetap diperbarui agar datanya konsisten)
            foreach ($order->items as $item) {
                Product::withTrashed()->whereKey($item->product_id)->increment('stock', $item->quantity);
            }

            $order->update(['status' => Order::CANCELLED]);

            $order->histories()->create([
                'status'     => Order::CANCELLED,
                'note'       => $reason,
                'changed_by' => $changedBy,
            ]);
        });
    }
}
