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

        // Pembeli kembali ke halaman ini setelah membayar, lewat salah satu dari dua jalur:
        //   - popup Snap yang selesai: ?cek=1
        //   - dialihkan oleh Midtrans (e-wallet, 3DS, dan sejenisnya): ?order_id=...&transaction_status=...
        // Parameter dari URL TIDAK dipercaya; ia hanya memicu pengecekan status langsung ke Midtrans.
        $returnedFromPayment = $request->boolean('cek') || $request->has('transaction_status');

        if ($returnedFromPayment && $order->status === Order::PENDING) {
            try {
                $this->payments->syncLatest($order);
                $order = $this->findOrder($request, $orderCode);
            } catch (Throwable $e) {
                session()->now('warning', 'Status pembayaran belum bisa dicek otomatis. Gunakan tombol "Cek status pembayaran".');
            }
        }

        // Halaman dimuat ulang oleh pembaruan otomatis (?diperbarui=1) atau setelah kembali dari pembayaran
        if ($returnedFromPayment || $request->boolean('diperbarui')) {
            if ($order->status === Order::PAID) {
                session()->now('success', 'Pembayaran berhasil diterima.');
            } elseif ($order->status === Order::CANCELLED) {
                session()->now('warning', 'Pesanan ini sudah dibatalkan.');
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
