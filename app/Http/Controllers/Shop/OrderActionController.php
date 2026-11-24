<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderManagementService;
use App\Services\OrderService;
use DomainException;
use Illuminate\Http\Request;

/**
 * Aksi pembeli pada pesanannya sendiri: membatalkan (sebelum dibayar) dan
 * mengonfirmasi barang sudah diterima.
 */
class OrderActionController extends Controller
{
    public function __construct(
        private OrderService $orders,
        private OrderManagementService $management,
    ) {
    }

    public function cancel(Request $request, string $orderCode)
    {
        $order = $this->ownOrder($request, $orderCode);

        try {
            $this->management->cancelUnpaid($order, 'Dibatalkan oleh pembeli.', $request->user()->id);
        } catch (DomainException $e) {
            return redirect()->route('orders.show', $order->order_code)->with('error', $e->getMessage());
        }

        return redirect()->route('orders.show', $order->order_code)
            ->with('success', 'Pesanan berhasil dibatalkan.');
    }

    public function receive(Request $request, string $orderCode)
    {
        $order = $this->ownOrder($request, $orderCode);

        try {
            $this->orders->complete($order, $request->user()->id, 'Pembeli mengonfirmasi pesanan sudah diterima.');
        } catch (DomainException $e) {
            return redirect()->route('orders.show', $order->order_code)->with('error', $e->getMessage());
        }

        return redirect()->route('orders.show', $order->order_code)
            ->with('success', 'Terima kasih! Pesanan ditandai selesai.');
    }

    /** Hanya pesanan milik user yang login; pesanan orang lain => 404 */
    private function ownOrder(Request $request, string $orderCode): Order
    {
        return Order::where('user_id', $request->user()->id)
            ->where('order_code', $orderCode)
            ->firstOrFail();
    }
}
