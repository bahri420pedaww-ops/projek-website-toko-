@extends('layouts.app')
@section('title', 'Keranjang Belanja - StyleShop')

@section('content')
<section class="bg-gradient-to-b from-brand-50 to-gray-50">
    <div class="max-w-7xl mx-auto px-4 py-10 md:py-14">
        <p class="text-sm font-semibold uppercase tracking-widest text-brand-600">StyleShop</p>
        <h1 class="mt-2 text-3xl md:text-4xl font-bold tracking-tight text-gray-900">Keranjang Belanja</h1>
        <p class="mt-2 text-gray-600">Periksa pilihanmu sebelum melanjutkan ke checkout.</p>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 py-8 md:py-12">
    @forelse ($items as $item)
        @if ($loop->first)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                <div class="lg:col-span-2 space-y-4">
                    <div class="hidden sm:grid grid-cols-12 px-5 text-xs font-semibold uppercase tracking-wider text-gray-500">
                        <span class="col-span-6">Produk</span>
                        <span class="col-span-2 text-center">Jumlah</span>
                        <span class="col-span-3 text-right">Subtotal</span>
                        <span class="col-span-1"></span>
                    </div>
        @endif

        <article class="rounded-2xl border border-gray-100 bg-white p-4 sm:p-5 shadow-sm transition hover:shadow-md">
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 sm:items-center">
                <div class="sm:col-span-6 flex min-w-0 items-center gap-4">
                    <a href="{{ route('products.show', $item['product']->slug) }}" class="h-24 w-24 shrink-0 overflow-hidden rounded-xl bg-gray-100 sm:h-28 sm:w-28">
                        @if ($item['product']->primaryImage)
                            <img src="{{ asset('storage/' . $item['product']->primaryImage->path) }}" alt="{{ $item['product']->name }}" class="h-full w-full object-cover">
                        @else
                            <span class="flex h-full w-full items-center justify-center text-xs text-gray-400">Foto produk</span>
                        @endif
                    </a>
                    <div class="min-w-0">
                        <a href="{{ route('products.show', $item['product']->slug) }}" class="line-clamp-2 font-semibold text-gray-900 hover:text-brand-600">
                            {{ $item['product']->name }}
                        </a>
                        <p class="mt-2 text-sm text-gray-500">Rp {{ number_format($item['product']->price, 0, ',', '.') }} / item</p>
                    </div>
                </div>

                <div class="sm:col-span-2 sm:text-center">
                    <span class="text-xs text-gray-500 sm:hidden">Jumlah</span>
                    <p class="mt-1 inline-flex min-w-12 items-center justify-center rounded-full bg-gray-100 px-3 py-1.5 text-sm font-semibold text-gray-700">
                        {{ $item['quantity'] }}
                    </p>
                </div>

                <div class="sm:col-span-3 sm:text-right">
                    <span class="text-xs text-gray-500 sm:hidden">Subtotal</span>
                    <p class="mt-1 font-bold text-gray-900">
                        Rp {{ number_format($item['product']->price * $item['quantity'], 0, ',', '.') }}
                    </p>
                </div>

                <div class="sm:col-span-1 sm:text-right">
                    <form action="{{ route('cart.destroy', $item['product']->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" aria-label="Hapus {{ $item['product']->name }} dari keranjang" title="Hapus produk"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-full text-gray-400 transition hover:bg-red-50 hover:text-red-600 focus:outline-none focus:ring-2 focus:ring-red-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12m-10 0 .7 12h6.6L16 7M9 7V4h6v3m-5 4v5m4-5v5" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </article>

        @if ($loop->last)
                </div>

                <aside class="lg:sticky lg:top-24 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900">Ringkasan Pesanan</h2>
                    <div class="mt-6 space-y-3 border-b border-gray-100 pb-5 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal produk</span>
                            <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Pengiriman</span>
                            <span class="text-gray-500">Dihitung saat checkout</span>
                        </div>
                    </div>
                    <div class="flex justify-between py-5 font-bold text-gray-900">
                        <span>Total sementara</span>
                        <span class="text-brand-600">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <a href="{{ route('checkout.create') }}" class="flex w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-3.5 font-semibold text-white shadow-sm transition hover:bg-brand-700 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-brand-100">
                        Lanjut ke Checkout
                        <span aria-hidden="true">→</span>
                    </a>
                    <a href="{{ route('products.index') }}" class="mt-4 block text-center text-sm font-medium text-gray-500 transition hover:text-brand-600">
                        Lanjut Belanja
                    </a>
                    <p class="mt-6 text-center text-xs leading-relaxed text-gray-400">Biaya pengiriman akan ditambahkan setelah kamu memilih layanan pengiriman.</p>
                </aside>
            </div>
        @endif
    @empty
        <div class="mx-auto max-w-xl rounded-3xl border border-gray-100 bg-white px-6 py-14 text-center shadow-sm">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l2.2 11.2a2 2 0 0 0 2 1.6h8.6a2 2 0 0 0 2-1.6L21 8H6M10 20a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm8 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" />
                </svg>
            </div>
            <h2 class="mt-5 text-xl font-bold text-gray-900">Keranjangmu masih kosong</h2>
            <p class="mt-2 text-gray-500">Yuk, temukan produk yang cocok untuk melengkapi gayamu.</p>
            <a href="{{ route('products.index') }}" class="mt-6 inline-flex items-center justify-center rounded-xl bg-brand-600 px-6 py-3 font-semibold text-white transition hover:bg-brand-700">
                Mulai Belanja
            </a>
        </div>
    @endforelse
</section>
@endsection
