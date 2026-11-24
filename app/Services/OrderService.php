<?php

namespace App\Services;

use App\Events\OrderShipped;
use App\Models\Order;
use App\Models\Product;
use App\Support\OrderStatus;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * Perubahan status pesanan yang punya efek samping (riwayat status, stok, resi).
 *
 * Alur status yang diizinkan:
 *   pending -> paid -> processing -> shipped -> completed
 *   pending -> cancelled        (pesanan yang sudah dibayar tidak bisa dibatalkan: tanpa refund)
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

    /** paid -> processing */
    public function startProcessing(Order $order, ?int $changedBy = null): void
    {
        DB::transaction(function () use ($order, $changedBy) {
            $order = $this->lockInStatus($order, Order::PAID, 'diproses');

            $order->update(['status' => Order::PROCESSING]);

            $order->histories()->create([
                'status'     => Order::PROCESSING,
                'note'       => 'Pesanan sedang disiapkan oleh penjual.',
                'changed_by' => $changedBy,
            ]);
        });
    }

    /** processing -> shipped, sekaligus mencatat nomor resi. */
    public function ship(Order $order, string $trackingNumber, ?int $changedBy = null): void
    {
        $trackingNumber = trim($trackingNumber);

        if ($trackingNumber === '') {
            throw new DomainException('Nomor resi wajib diisi.');
        }

        $shipped = DB::transaction(function () use ($order, $trackingNumber, $changedBy) {
            $order = $this->lockInStatus($order, Order::PROCESSING, 'dikirim');

            $order->shipment()->updateOrCreate([], [
                'tracking_number' => $trackingNumber,
                'shipped_at'      => now(),
            ]);

            $order->update(['status' => Order::SHIPPED]);

            $order->histories()->create([
                'status'     => Order::SHIPPED,
                'note'       => "Pesanan dikirim. No. resi: {$trackingNumber}.",
                'changed_by' => $changedBy,
            ]);

            return $order;
        });

        // Event dipicu SETELAH transaksi database selesai
        event(new OrderShipped($shipped->fresh()));
    }

    /** shipped -> completed (admin menandai selesai, atau pembeli mengonfirmasi barang diterima). */
    public function complete(Order $order, ?int $changedBy = null, ?string $note = null): void
    {
        DB::transaction(function () use ($order, $changedBy, $note) {
            $order = $this->lockInStatus($order, Order::SHIPPED, 'diselesaikan');

            $order->shipment?->update(['delivered_at' => now()]);

            $order->update(['status' => Order::COMPLETED]);

            $order->histories()->create([
                'status'     => Order::COMPLETED,
                'note'       => $note ?? 'Pesanan selesai.',
                'changed_by' => $changedBy,
            ]);
        });
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

    /** Kunci pesanan dan pastikan statusnya sesuai; kalau tidak, transisi ditolak. */
    private function lockInStatus(Order $order, string $expected, string $actionLabel): Order
    {
        $locked = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();

        if ($locked->status !== $expected) {
            throw new DomainException(
                "Pesanan tidak bisa {$actionLabel} karena statusnya sekarang \""
                . OrderStatus::label($locked->status) . '".'
            );
        }

        return $locked;
    }
}
