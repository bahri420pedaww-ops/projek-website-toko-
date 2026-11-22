<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\CartService;
use DomainException;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private CartService $cart)
    {
    }

    public function index(Request $request)
    {
        $cart = $this->cart->summary($request->user());

        return view('shop.cart.index', compact('cart'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity'   => ['nullable', 'integer', 'min:1', 'max:' . CartService::MAX_QTY],
        ]);

        $product = Product::findOrFail($data['product_id']); // produk terhapus => 404

        try {
            $notice = $this->cart->add($request->user(), $product, $data['quantity'] ?? 1);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('cart.index')
            ->with('success', 'Produk ditambahkan ke keranjang.')
            ->with('warning', $notice);
    }

    public function update(Request $request, int $item)
    {
        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:' . CartService::MAX_QTY],
        ]);

        try {
            $notice = $this->cart->update($request->user(), $item, $data['quantity']);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()
            ->with('success', 'Jumlah diperbarui.')
            ->with('warning', $notice);
    }

    public function destroy(Request $request, int $item)
    {
        $this->cart->remove($request->user(), $item);

        return back()->with('success', 'Produk dihapus dari keranjang.');
    }

    public function clear(Request $request)
    {
        $this->cart->clear($request->user());

        return back()->with('success', 'Keranjang dikosongkan.');
    }
}
