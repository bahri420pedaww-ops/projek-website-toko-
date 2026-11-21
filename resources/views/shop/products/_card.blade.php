{{-- PLACEHOLDER (backend). Variabel: $product (dengan category dan primaryImage) --}}
<a href="{{ route('shop.products.show', $product->slug) }}"
   class="block bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition">
    @if ($product->primaryImage)
        <img src="{{ asset('storage/' . $product->primaryImage->path) }}"
             alt="{{ $product->name }}" class="h-48 w-full object-cover">
    @else
        <div class="h-48 w-full bg-gray-200 flex items-center justify-center text-sm text-gray-500">
            Tidak ada gambar
        </div>
    @endif

    <div class="p-4">
        <p class="text-xs text-gray-500">{{ $product->category?->name }}</p>
        <h3 class="font-medium">{{ $product->name }}</h3>
        <p class="mt-1 font-semibold">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
        @if ($product->stock < 1)
            <span class="text-xs font-semibold text-red-600">Stok habis</span>
        @endif
    </div>
</a>
