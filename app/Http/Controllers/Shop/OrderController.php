<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Throwable;

/**
 * Riwayat pesanan milik pembeli yang sedang login.
 */
class OrderController extends Controller
{
    public function __construct(private PaymentService $payments)
    {
    }

    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->withCount('items')
            ->latest('id')
            ->paginate(10);

        return view('shop.orders.index', compact('orders'));
    }

    public function show(Request $request, string $orderCode)
    {
        $order = $this->findOrder($request, $orderCode);

        // Setelah popup pembayaran selesai, pembeli dikembalikan ke sini dengan ?cek=1.
        // Status ditanyakan langsung ke Midtrans supaya langsung terbarui tanpa menunggu webhook.
        if ($request->boolean('cek') && $order->status === Order::PENDING) {
            try {
                $this->payments->syncLatest($order);
                $order = $this->findOrder($request, $orderCode);

                if ($order->status === Order::PAID) {
                    session()->now('success', 'Pembayaran berhasil diterima.');
                }
            } catch (Throwable $e) {
                session()->now('warning', 'Status pembayaran belum bisa dicek otomatis. Gunakan tombol "Cek status pembayaran".');
            }
        }

        return view('shop.orders.show', compact('order'));
    }

    /** Hanya pesanan milik user yang login; pesanan orang lain => 404 */
    private function findOrder(Request $request, string $orderCode): Order
    {
        return Order::where('user_id', $request->user()->id)
            ->where('order_code', $orderCode)
            ->with(['items', 'histories', 'shippingMethod', 'shipment', 'payments'])
            ->firstOrFail();
    }
}
