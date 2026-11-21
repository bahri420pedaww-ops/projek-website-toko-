{{-- PLACEHOLDER (backend). Variabel: $product
     Kontrak form: POST ke route('cart.store') dengan field product_id dan quantity.
     Tamu (belum login) diarahkan ke halaman login. --}}
@if ($product->stock < 1)
    <button type="button" disabled
            class="mt-5 px-6 py-3 bg-gray-300 text-gray-600 text-sm font-semibold rounded-md cursor-not-allowed">
        Stok habis
    </button>
@else
    @auth
        <form method="POST" action="{{ route('cart.store') }}" class="mt-5 flex items-center gap-3">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="number" name="quantity" value="1" min="1" max="{{ min($product->stock, 99) }}"
                   class="w-24 border-gray-300 rounded-md shadow-sm text-sm">
            <button class="px-6 py-3 bg-gray-800 text-white text-sm font-semibold rounded-md hover:bg-gray-700">
                Tambah ke Keranjang
            </button>
        </form>
    @else
        <a href="{{ route('login') }}"
           class="mt-5 inline-block px-6 py-3 bg-gray-800 text-white text-sm font-semibold rounded-md hover:bg-gray-700">
            Masuk untuk membeli
        </a>
    @endauth
@endif
