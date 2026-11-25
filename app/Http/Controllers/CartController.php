<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $cart = $request->session()->get('cart', []);
        $products = Product::active()->with('primaryImage')
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        $items = collect($cart)->map(function ($quantity, $productId) use ($products) {
            return [
                'product' => $products->get($productId),
                'quantity' => $quantity,
            ];
        })->filter(function ($item) {
            return $item['product'] !== null;
        })->values();

        return view('cart', [
            'items' => $items,
            'subtotal' => $items->sum(function ($item) {
                return $item['product']->price * $item['quantity'];
            }),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $product = Product::active()->findOrFail($validated['product_id']);
        $cart = $request->session()->get('cart', []);
        $quantity = (int) ($cart[$product->id] ?? 0) + $validated['quantity'];

        if ($quantity > 1000 || $quantity > $product->stock) {
            return back()->withErrors([
                'quantity' => 'Jumlah yang diminta melebihi stok atau batas per produk.',
            ])->withInput();
        }

        $cart[$product->id] = $quantity;
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Produk ditambahkan ke keranjang.');
    }

    public function destroy(Request $request, int $productId): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$productId]);
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Produk dihapus dari keranjang.');
    }
}
