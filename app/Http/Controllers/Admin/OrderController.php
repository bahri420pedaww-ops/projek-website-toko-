<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderStatusHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        return view('admin.orders.index', [
            'orders' => Order::with('user')->latest()->paginate(20),
            'statuses' => [
                Order::PENDING,
                Order::PROCESSING,
                Order::SHIPPED,
                Order::COMPLETED,
                Order::CANCELLED,
            ],
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        return $this->updateStatus($request, $order);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in([
                Order::PENDING,
                Order::PROCESSING,
                Order::SHIPPED,
                Order::COMPLETED,
                Order::CANCELLED,
            ])],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($request, $order, $validated) {
            $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if ($order->status === Order::CANCELLED && $validated['status'] !== Order::CANCELLED) {
                throw ValidationException::withMessages([
                    'status' => 'Pesanan yang dibatalkan tidak dapat diaktifkan kembali.',
                ]);
            }
            if ($validated['status'] === Order::CANCELLED && $order->status !== Order::CANCELLED) {
                $items = $order->items;
                $products = Product::withTrashed()->whereIn('id', $items->pluck('product_id'))
                    ->orderBy('id')->lockForUpdate()->get()->keyBy('id');

                foreach ($items as $item) {
                    $products->get($item->product_id)->increment('stock', $item->quantity);
                }
            }
            $order->update(['status' => $validated['status']]);
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $validated['status'],
                'note' => $validated['note'] ?? null,
                'changed_by' => $request->user()->id,
            ]);
        });

        return redirect()->route('admin.orders.index')->with('success', 'Status pesanan berhasil diperbarui.');
    }
}
