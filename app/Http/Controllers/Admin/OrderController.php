<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderManagementService;
use App\Services\OrderService;
use App\Support\OrderStatus;
use DomainException;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orders,
        private OrderManagementService $management,
    ) {
    }

    public function index(Request $request)
    {
        $status = $request->input('status');
        $status = array_key_exists((string) $status, OrderStatus::LABELS) ? $status : null;
        $keyword = trim((string) $request->input('q'));

        $orders = Order::query()
            ->with('user')
            ->withCount('items')
            ->when($status, fn ($q, $s) => $q->where('status', $s))
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($w) use ($keyword) {
                    $w->where('order_code', 'like', "%{$keyword}%")
                      ->orWhere('receiver_name', 'like', "%{$keyword}%")
                      ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$keyword}%")
                                                         ->orWhere('email', 'like', "%{$keyword}%"));
                });
            })
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $counts = Order::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('admin.orders.index', compact('orders', 'status', 'keyword', 'counts'));
    }

    public function show(Order $order)
    {
        $order->load([
            'user', 'items', 'shippingMethod', 'shipment', 'payments',
            'histories.changedBy',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    /** paid -> processing */
    public function process(Request $request, Order $order)
    {
        try {
            $this->orders->startProcessing($order, $request->user()->id);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pesanan masuk tahap diproses.');
    }

    /** processing -> shipped (wajib nomor resi) */
    public function ship(Request $request, Order $order)
    {
        $data = $request->validate([
            'tracking_number' => ['required', 'string', 'max:100'],
        ], [
            'tracking_number.required' => 'Nomor resi wajib diisi.',
        ]);

        try {
            $this->orders->ship($order, $data['tracking_number'], $request->user()->id);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pesanan ditandai dikirim.');
    }

    /** shipped -> completed */
    public function complete(Request $request, Order $order)
    {
        try {
            $this->orders->complete($order, $request->user()->id, 'Pesanan ditandai selesai oleh admin.');
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pesanan ditandai selesai.');
    }

    /** pending -> cancelled (stok dikembalikan) */
    public function cancel(Request $request, Order $order)
    {
        try {
            $this->management->cancelUnpaid($order, 'Dibatalkan oleh admin.', $request->user()->id);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Pesanan dibatalkan dan stok dikembalikan.');
    }
}
