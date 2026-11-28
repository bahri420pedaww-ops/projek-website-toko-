@extends('layouts.app')

@section('content')
@php
    $placeholder = 'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=1200&q=80';
@endphp

<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">

    {{-- Breadcrumb --}}
    <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500" aria-label="Breadcrumb">
        <a href="{{ url('/') }}" class="transition hover:text-brand-600">Beranda</a>
        <svg class="h-4 w-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        <span class="truncate font-medium text-gray-900">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2 lg:gap-14">

        {{-- KOLOM KIRI: Gambar --}}
        <div class="lg:sticky lg:top-24 lg:self-start">
            <div class="group aspect-[4/5] overflow-hidden rounded-3xl bg-gray-100 shadow-sm ring-1 ring-gray-100">
                @if ($product->primaryImage && $product->primaryImage->path)
                    <img src="{{ \Illuminate\Support\Str::startsWith($product->primaryImage->path, ['http://', 'https://']) ? $product->primaryImage->path : asset('storage/' . ltrim($product->primaryImage->path, '/')) }}"
                         alt="{{ $product->name }}" onerror="this.onerror=null;this.src='{{ $placeholder }}';"
                         class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
                @else
                    <img src="{{ $placeholder }}" alt="{{ $product->name }}"
                         class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105">
                @endif
            </div>
        </div>

        {{-- KOLOM KANAN: Info --}}
        <div class="flex flex-col">
            <span class="inline-flex w-fit items-center rounded-full bg-brand-600/10 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-brand-600">
                StyleShop Original
            </span>

            <h1 class="mt-4 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                {{ $product->name }}
            </h1>

            <p class="mt-4 text-3xl font-bold text-brand-600">
                Rp {{ number_format($product->price, 0, ',', '.') }}
            </p>

            @if (isset($product->stock))
                <p class="mt-3 inline-flex items-center gap-2 text-sm text-gray-500">
                    <span class="h-2 w-2 rounded-full {{ $product->stock > 0 ? 'bg-green-500' : 'bg-red-500' }}"></span>
                    {{ $product->stock > 0 ? 'Stok tersedia: ' . $product->stock : 'Stok habis' }}
                </p>
            @endif

            <div class="my-6 h-px bg-gray-100"></div>

            @if (!empty($product->description))
                <div>
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-900">Deskripsi</h2>
                    <p class="mt-3 whitespace-pre-line text-base leading-relaxed text-gray-600">{{ $product->description }}</p>
                </div>
            @endif

            {{-- FORM AKSI --}}
            <form action="{{ route('cart.store') }}" method="POST" class="mt-8">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                {{-- Jumlah --}}
                <label for="quantity" class="block text-sm font-semibold text-gray-900">Jumlah</label>
                <div class="mt-2 inline-flex items-center rounded-xl border border-gray-200 bg-white shadow-sm">
                    <button type="button" id="qty-minus" aria-label="Kurangi jumlah"
                            class="flex h-12 w-12 items-center justify-center rounded-l-xl text-xl text-gray-600 transition hover:bg-gray-50 hover:text-brand-600">&minus;</button>
                    <input type="number" id="quantity" name="quantity" value="1" min="1"
                           @if(isset($product->stock)) max="{{ $product->stock }}" @endif
                           class="h-12 w-16 border-0 border-x border-gray-200 bg-transparent text-center text-base font-semibold text-gray-900 focus:border-brand-600 focus:outline-none focus:ring-0 [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">
                    <button type="button" id="qty-plus" aria-label="Tambah jumlah"
                            class="flex h-12 w-12 items-center justify-center rounded-r-xl text-xl text-gray-600 transition hover:bg-gray-50 hover:text-brand-600">+</button>
                </div>

                {{-- Tombol --}}
                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <button type="submit"
                            class="inline-flex h-14 flex-1 items-center justify-center gap-2 rounded-xl bg-gray-900 px-6 text-base font-semibold text-white transition hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-900/20 active:scale-[0.98]">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                        </svg>
                        Tambah ke Keranjang
                    </button>

                    <button type="submit" formaction="{{ route('cart.buyNow') }}"
                            class="inline-flex h-14 flex-1 items-center justify-center gap-2 rounded-xl bg-brand-600 px-6 text-base font-semibold text-white shadow-lg shadow-brand-600/30 transition hover:bg-brand-700 focus:outline-none focus:ring-4 focus:ring-brand-600/30 active:scale-[0.98]">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                        </svg>
                        Beli Langsung
                    </button>
                </div>
            </form>

            {{-- Jaminan --}}
            <div class="mt-8 grid grid-cols-3 gap-3 border-t border-gray-100 pt-6 text-center text-xs text-gray-500">
                <div class="rounded-xl bg-gray-50 px-2 py-3">
                    <p class="font-semibold text-gray-900">Kualitas Terjamin</p>
                    <p class="mt-0.5">Bahan pilihan</p>
                </div>
                <div class="rounded-xl bg-gray-50 px-2 py-3">
                    <p class="font-semibold text-gray-900">Aman</p>
                    <p class="mt-0.5">Transaksi terlindungi</p>
                </div>
                <div class="rounded-xl bg-gray-50 px-2 py-3">
                    <p class="font-semibold text-gray-900">Pengiriman</p>
                    <p class="mt-0.5">Cepat &amp; terpercaya</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const input = document.getElementById('quantity');
        const min = parseInt(input.min || '1', 10);
        const max = input.max ? parseInt(input.max, 10) : Infinity;
        document.getElementById('qty-minus').addEventListener('click', () => {
            input.value = Math.max(min, (parseInt(input.value, 10) || min) - 1);
        });
        document.getElementById('qty-plus').addEventListener('click', () => {
            input.value = Math.min(max, (parseInt(input.value, 10) || min) + 1);
        });
    })();
</script>
@endsection