<?php

namespace App\Http\Controllers\Shop;

use App\Exceptions\MidtransException;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PaymentService;
use App\Support\OrderStatus;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Throwable;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $payments)
    {
    }

    /** Halaman pembayaran: menyiapkan token Snap lalu membuka popup pembayaran Midtrans. */
    public function show(Request $request, string $orderCode)
    {
        $order = $this->ownOrder($request, $orderCode);

        if ($order->status !== Order::PENDING) {
            return redirect()->route('orders.show', $order->order_code)
                ->with('warning', 'Pesanan ini tidak sedang menunggu pembayaran.');
        }

        try {
            $payment = $this->payments->startPayment($order);
        } catch (DomainException | MidtransException $e) {
            return redirect()->route('orders.show', $order->order_code)->with('error', $e->getMessage());
        }

        return view('shop.payments.show', [
            'order'     => $order,
            'payment'   => $payment,
            'clientKey' => (string) config('midtrans.client_key'),
            'snapJsUrl' => (string) config('midtrans.snap_js_url'),
        ]);
    }

    /** Tombol "Cek status pembayaran": tanya langsung ke Midtrans (tidak butuh webhook). */
    public function check(Request $request, string $orderCode)
    {
        $order = $this->ownOrder($request, $orderCode);

        try {
            $payment = $this->payments->syncLatest($order);
        } catch (MidtransException $e) {
            return redirect()->route('orders.show', $order->order_code)->with('error', $e->getMessage());
        }

        $order->refresh();

        $redirect = redirect()->route('orders.show', $order->order_code);

        if ($order->status === Order::PAID) {
            return $redirect->with('success', 'Pembayaran berhasil diterima.');
        }

        if ($order->status === Order::CANCELLED) {
            return $redirect->with('error', 'Pesanan dibatalkan karena pembayaran tidak dilakukan tepat waktu.');
        }

        if (! $payment) {
            return $redirect->with('warning', 'Belum ada transaksi pembayaran. Klik "Bayar sekarang" untuk memulai.');
        }

        return $redirect->with(
            'warning',
            'Pembayaran belum diterima. Selesaikan pembayaran, lalu cek lagi beberapa saat kemudian.'
        );
    }

    /**
     * Status pesanan dalam bentuk JSON. Dipanggil otomatis oleh halaman pesanan dan halaman
     * bayar setiap beberapa detik selama pesanan masih menunggu pembayaran, sehingga status
     * berubah sendiri tanpa pembeli menekan tombol apa pun.
     *
     * Kalau webhook Midtrans tidak sampai (misalnya di localhost), pengecekan ini yang menanyakan
     * status ke Midtrans. Dibatasi maksimal satu pertanyaan ke Midtrans per 4 detik per pesanan.
     */
    public function status(Request $request, string $orderCode)
    {
        $order = $this->ownOrder($request, $orderCode);

        if ($order->status === Order::PENDING && Cache::add("order-sync:{$order->id}", 1, 4)) {
            try {
                $this->payments->syncLatest($order);
                $order->refresh();
            } catch (Throwable $e) {
                // Diabaikan: pengecekan berikutnya, webhook, atau tombol cek manual tetap bisa dipakai
            }
        }

        return response()->json([
            'status'         => $order->status,
            'label'          => OrderStatus::label($order->status),
            'payment_status' => $order->payments()->latest('id')->value('status'),
        ]);
    }

    private function ownOrder(Request $request, string $orderCode): Order
    {
        return Order::where('user_id', $request->user()->id)
            ->where('order_code', $orderCode)
            ->firstOrFail();
    }
}
