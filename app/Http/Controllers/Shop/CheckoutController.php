<?php

namespace App\Http\Controllers\Shop;

use App\Events\OrderPlaced;
use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\ShippingCalculator;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cart,
        private CheckoutService $checkout,
        private ShippingCalculator $calculator,
    ) {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $cart = $this->cart->summary($user);

        if ($cart['is_empty']) {
            return redirect()->route('cart.index')->with('error', 'Keranjang masih kosong.');
        }

        if ($cart['has_problem']) {
            return redirect()->route('cart.index')
                ->with('error', 'Perbaiki produk yang bermasalah di keranjang sebelum checkout.');
        }

        $addresses = $user->addresses()->orderByDesc('is_default')->latest('id')->get();

        if ($addresses->isEmpty()) {
            return redirect()->route('addresses.create')
                ->with('warning', 'Tambahkan alamat pengiriman dulu, lalu kembali ke keranjang untuk checkout.');
        }

        $shippingMethods = ShippingMethod::active()
            ->with('rates')
            ->orderBy('courier')
            ->orderBy('service')
            ->get();

        if ($shippingMethods->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Belum ada layanan pengiriman yang aktif. Hubungi admin toko.');
        }

        // Berat total keranjang (gram)
        $weightGram = (int) $cart['items']->sum(fn ($line) => $line->product->weight_gram * $line->quantity);

        // Ongkir untuk SETIAP kombinasi alamat x layanan, supaya halaman bisa menampilkan
        // ongkir yang benar saat pembeli berganti alamat tanpa memuat ulang halaman.
        // $quotes[alamat_id][layanan_id] = ['zone', 'billable_kg', 'cost', 'etd'] atau null
        $quotes = [];
        foreach ($addresses as $address) {
            foreach ($shippingMethods as $method) {
                $quotes[$address->id][$method->id] = $this->calculator->quote(
                    $method, $address->city, $address->province, $weightGram
                );
            }
        }

        $billableKg = $this->calculator->billableKg($weightGram);

        return view('shop.checkout.index', compact(
            'cart', 'addresses', 'shippingMethods', 'quotes', 'weightGram', 'billableKg'
        ));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            // Alamat hanya boleh milik user yang login dan belum dihapus
            'address_id' => [
                'required', 'integer',
                Rule::exists('addresses', 'id')->where('user_id', $user->id)->whereNull('deleted_at'),
            ],
            'shipping_method_id' => [
                'required', 'integer',
                Rule::exists('shipping_methods', 'id')->where('is_active', true),
            ],
            'note' => ['nullable', 'string', 'max:255'],
        ], [
            'address_id.required'         => 'Pilih alamat pengiriman.',
            'address_id.exists'           => 'Alamat pengiriman tidak valid.',
            'shipping_method_id.required' => 'Pilih layanan pengiriman.',
            'shipping_method_id.exists'   => 'Layanan pengiriman tidak valid.',
        ]);

        $address = $user->addresses()->findOrFail($data['address_id']);
        $shipping = ShippingMethod::findOrFail($data['shipping_method_id']);

        try {
            $order = $this->checkout->placeOrder($user, $address, $shipping, $data['note'] ?? null);
        } catch (DomainException $e) {
            return redirect()->route('checkout.index')->with('error', $e->getMessage());
        }

        // Dipicu SETELAH pesanan tersimpan; email dikirim lewat listener (kegagalan email
        // tidak mempengaruhi pesanan).
        event(new OrderPlaced($order));

        return redirect()->route('orders.show', $order->order_code)
            ->with('success', 'Pesanan berhasil dibuat. Silakan lakukan pembayaran.');
    }
}
