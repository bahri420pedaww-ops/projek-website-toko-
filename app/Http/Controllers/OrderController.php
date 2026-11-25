<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\ShippingMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if (empty($request->session()->get('cart', []))) {
            return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong.');
        }

        return view('checkout', [
            'shippingMethods' => ShippingMethod::active()->orderBy('cost')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'shipping_method_id' => [
                'required',
                'integer',
                Rule::exists('shipping_methods', 'id')->where('is_active', true),
            ],
            'receiver_name' => ['required', 'string', 'max:255'],
            'receiver_phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+().\\-\\s]+$/'],
            'shipping_address' => ['required', 'string', 'max:2000'],
            'city_name' => ['required', 'string', 'max:255'],
            'province_name' => ['required', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:10', 'regex:/^[A-Za-z0-9\\s-]+$/'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $cart = $request->session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong.');
        }

        ksort($cart);
        $order = DB::transaction(function () use ($request, $validated, $cart) {
            $shippingMethod = ShippingMethod::active()
                ->whereKey($validated['shipping_method_id'])
                ->firstOrFail();

            $products = Product::query()->whereIn('id', array_keys($cart))
                ->orderBy('id')->lockForUpdate()->get()->keyBy('id');

            if ($products->count() !== count($cart)) {
                abort(422, 'Salah satu produk di keranjang sudah tidak tersedia.');
            }

            $subtotal = 0;
            foreach ($cart as $productId => $quantity) {
                $product = $products->get($productId);
                if (!$product->is_active || !is_int($quantity) || $quantity < 1 || $quantity > 1000 || $quantity > $product->stock) {
                    abort(422, 'Stok produk tidak mencukupi atau produk sudah tidak aktif.');
                }
                if ($product->price > intdiv(PHP_INT_MAX - $subtotal, $quantity)) {
                    abort(422, 'Nilai pesanan melebihi batas yang dapat diproses.');
                }
                $subtotal += $product->price * $quantity;
            }
            if ($shippingMethod->cost > PHP_INT_MAX - $subtotal) {
                abort(422, 'Nilai pesanan melebihi batas yang dapat diproses.');
            }

            $order = Order::create([
                'order_code' => Order::generateCode(),
                'user_id' => $request->user()->id,
                'shipping_method_id' => $shippingMethod->id,
                'receiver_name' => $validated['receiver_name'],
                'receiver_phone' => $validated['receiver_phone'],
                'shipping_address' => $validated['shipping_address'],
                'city_name' => $validated['city_name'],
                'province_name' => $validated['province_name'],
                'postal_code' => $validated['postal_code'] ?? null,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingMethod->cost,
                'discount_total' => 0,
                'grand_total' => $subtotal + $shippingMethod->cost,
                'status' => Order::PENDING,
                'note' => $validated['note'] ?? null,
            ]);

            foreach ($cart as $productId => $quantity) {
                $product = $products->get($productId);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $quantity,
                ]);
                $product->decrement('stock', $quantity);
            }

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => Order::PENDING,
                'note' => 'Pesanan dibuat.',
                'changed_by' => $request->user()->id,
            ]);

            return $order;
        });

        $request->session()->forget('cart');

        return redirect()->route('home')->with('success', 'Pesanan ' . $order->order_code . ' berhasil dibuat.');
    }
}
