@extends('layouts.app')

@section('content')

@php
    $placeholder = 'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=1200&q=80';

    // Ambil koleksi produk (aman untuk collection maupun paginator)
    $productList = $products instanceof \Illuminate\Pagination\AbstractPaginator
        ? $products->getCollection()
        : collect($products);
    $heroA = $productList->get(0);
    $heroB = $productList->get(1);
@endphp

<style>
    html { scroll-behavior: smooth; }
    .ss-container { width: 100%; max-width: 1400px; margin-left: auto; margin-right: auto; padding-left: 1.5rem; padding-right: 1.5rem; }
    @media (min-width: 1024px) { .ss-container { padding-left: 2rem; padding-right: 2rem; } }
    @keyframes ss-float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
    .ss-float { animation: ss-float 5s ease-in-out infinite; }
    .ss-float-delay { animation: ss-float 6s ease-in-out infinite; animation-delay: 1.2s; }
    .ss-dots { background-image: radial-gradient(rgba(17,24,39,.07) 1px, transparent 1px); background-size: 22px 22px; }
</style>

{{-- ================= HERO ================= --}}
<section class="relative overflow-hidden bg-gradient-to-b from-gray-50 via-white to-white">
    <div class="ss-dots pointer-events-none absolute inset-0 opacity-70"></div>
    <div class="pointer-events-none absolute -right-32 -top-32 h-[28rem] w-[28rem] rounded-full bg-brand-600/15 blur-3xl"></div>
    <div class="pointer-events-none absolute -left-32 bottom-0 h-80 w-80 rounded-full bg-gray-900/5 blur-3xl"></div>

    <div class="ss-container relative py-14 sm:py-20 lg:py-24">
        <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-2 lg:gap-16">

            {{-- Teks --}}
            <div>
                <span class="inline-flex items-center gap-2 rounded-full border border-brand-600/20 bg-white/80 px-4 py-1.5 text-xs font-semibold uppercase tracking-[0.18em] text-brand-600 shadow-sm backdrop-blur">
                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-brand-600"></span>
                    Koleksi Terbaru
                </span>

                <h1 class="mt-6 text-4xl font-bold leading-[1.1] tracking-tight text-gray-900 sm:text-5xl lg:text-6xl">
                    Tampil percaya diri dengan
                    <span class="relative whitespace-nowrap text-brand-600">
                        StyleShop
                        <svg class="absolute -bottom-2 left-0 h-3 w-full text-brand-600/30" viewBox="0 0 200 12" preserveAspectRatio="none" fill="none">
                            <path d="M2 9C50 3 150 3 198 9" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                        </svg>
                    </span>
                </h1>

                <p class="mt-6 max-w-xl text-base leading-relaxed text-gray-500 sm:text-lg">
                    Pilihan pakaian berkualitas dengan desain modern dan nyaman dipakai untuk setiap momen harimu.
                </p>

                <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                    <a href="#produk"
                       class="inline-flex h-14 items-center justify-center gap-2 rounded-xl bg-brand-600 px-8 text-base font-semibold text-white shadow-lg shadow-brand-600/30 transition duration-300 hover:-translate-y-0.5 hover:bg-brand-700 hover:shadow-xl hover:shadow-brand-600/40 focus:outline-none focus:ring-4 focus:ring-brand-600/30">
                        Belanja Sekarang
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                    <a href="#keunggulan"
                       class="inline-flex h-14 items-center justify-center rounded-xl border border-gray-200 bg-white px-8 text-base font-semibold text-gray-900 shadow-sm transition duration-300 hover:-translate-y-0.5 hover:border-gray-900 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-gray-900/10">
                        Kenapa StyleShop
                    </a>
                </div>
            </div>

            {{-- Kolase visual --}}
            <div class="relative mx-auto h-[420px] w-full max-w-md sm:h-[520px] lg:max-w-none">
                <div class="absolute inset-6 rounded-[2.5rem] bg-gradient-to-br from-brand-600/15 via-brand-600/5 to-transparent"></div>

                {{-- Foto besar --}}
                <div class="absolute right-0 top-0 w-[66%] overflow-hidden rounded-3xl bg-gray-100 shadow-2xl shadow-gray-900/15 ring-1 ring-black/5">
                    <div class="aspect-[4/5]">
                        @if ($heroA && $heroA->primaryImage && $heroA->primaryImage->path)
                            <img src="{{ \Illuminate\Support\Str::startsWith($heroA->primaryImage->path, ['http://', 'https://']) ? $heroA->primaryImage->path : asset('storage/' . ltrim($heroA->primaryImage->path, '/')) }}"
                                 alt="{{ $heroA->name }}" onerror="this.onerror=null;this.src='{{ $placeholder }}';"
                                 class="h-full w-full object-cover">
                        @else
                            <img src="{{ $placeholder }}" alt="Koleksi StyleShop" class="h-full w-full object-cover">
                        @endif
                    </div>
                </div>

                {{-- Foto kecil --}}
                <div class="absolute bottom-0 left-0 w-[48%] overflow-hidden rounded-3xl border-4 border-white bg-gray-100 shadow-2xl shadow-gray-900/15">
                    <div class="aspect-square">
                        @if ($heroB && $heroB->primaryImage && $heroB->primaryImage->path)
                            <img src="{{ \Illuminate\Support\Str::startsWith($heroB->primaryImage->path, ['http://', 'https://']) ? $heroB->primaryImage->path : asset('storage/' . ltrim($heroB->primaryImage->path, '/')) }}"
                                 alt="{{ $heroB->name }}" onerror="this.onerror=null;this.src='{{ $placeholder }}';"
                                 class="h-full w-full object-cover">
                        @else
                            <img src="{{ $placeholder }}" alt="Koleksi StyleShop" class="h-full w-full object-cover">
                        @endif
                    </div>
                </div>

                {{-- Badge melayang 1 --}}
                <div class="ss-float absolute left-0 top-10 flex items-center gap-3 rounded-2xl bg-white/95 px-4 py-3 shadow-xl shadow-gray-900/10 ring-1 ring-black/5 backdrop-blur sm:left-[-1.5rem]">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-600 text-white">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
                    </span>
                    <div>
                        <p class="text-sm font-bold text-gray-900">Kualitas Premium</p>
                        <p class="text-xs text-gray-500">Bahan pilihan</p>
                    </div>
                </div>

                {{-- Badge melayang 2 --}}
                <div class="ss-float-delay absolute bottom-16 right-0 flex items-center gap-3 rounded-2xl bg-gray-900 px-4 py-3 text-white shadow-xl shadow-gray-900/30 sm:right-[-1rem]">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
                    </span>
                    <div>
                        <p class="text-sm font-bold">Desain Modern</p>
                        <p class="text-xs text-gray-300">Siap dipakai harian</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================= KEUNGGULAN ================= --}}
<section id="keunggulan" class="scroll-mt-24 border-y border-gray-100 bg-white">
    <div class="ss-container py-8 sm:py-10">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
            <div class="flex items-center gap-4">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-brand-600/10 text-brand-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                </span>
                <div>
                    <p class="font-semibold text-gray-900">Kualitas Terjamin</p>
                    <p class="text-sm text-gray-500">Bahan pilihan dan nyaman</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-brand-600/10 text-brand-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                </span>
                <div>
                    <p class="font-semibold text-gray-900">Pembayaran Aman</p>
                    <p class="text-sm text-gray-500">Transfer bank &amp; COD</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-brand-600/10 text-brand-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>
                </span>
                <div>
                    <p class="font-semibold text-gray-900">Pengiriman Terpercaya</p>
                    <p class="text-sm text-gray-500">Sampai dengan aman</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ================= KATEGORI ================= --}}
<section id="kategori" class="scroll-mt-24 border-b border-gray-100 bg-white">
    <div class="ss-container py-12 sm:py-14">
        <div class="mb-7">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600">Jelajahi</p>
            <h2 class="mt-2 text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">Kategori Pilihan</h2>
        </div>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
            @forelse ($categories as $category)
                @php
                    $categoryKey = \Illuminate\Support\Str::lower(($category->slug ?? '') . ' ' . ($category->name ?? ''));
                    $categoryIcon = \Illuminate\Support\Str::contains($categoryKey, 'kaos')
                        ? 'kaos'
                        : (\Illuminate\Support\Str::contains($categoryKey, 'kemeja')
                            ? 'kemeja'
                            : (\Illuminate\Support\Str::contains($categoryKey, 'celana') ? 'celana' : 'default'));
                @endphp
                <a href="{{ route('products.index', ['category' => $category->slug]) }}"
                   class="group flex min-h-20 items-center justify-between gap-3 rounded-2xl border border-gray-100 bg-gray-50 px-4 py-4 transition duration-300 hover:border-brand-600/40 hover:bg-brand-50 hover:shadow-md">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-white text-brand-600 shadow-sm">
                            @if ($categoryIcon === 'kaos')
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 3.75 2.25 3.75-2.25 4.5 2.25-2.25 4.5-2.25-1.125V20.25h-7.5V10.125L6 11.25l-2.25-4.5 4.5-2.25Z"/>
                                </svg>
                            @elseif ($categoryIcon === 'kemeja')
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 3.75 2.25 3.75-2.25 4.5 2.25-2.25 4.5-2.25-1.125V20.25h-7.5V10.125L6 11.25l-2.25-4.5 4.5-2.25Zm3.75 2.25v13.5m0-10.5h.008m-.008 3h.008m-.008 3h.008"/>
                                </svg>
                            @elseif ($categoryIcon === 'celana')
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 3.75h9l1.5 16.5h-4.5L12 12l-1.5 8.25H6l1.5-16.5Z"/>
                                </svg>
                            @else
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 13.5 13.5 20.25 3.75 10.5V3.75H10.5l9.75 9.75ZM7.5 7.5h.008v.008H7.5V7.5Z"/>
                                </svg>
                            @endif
                        </div>
                        <span class="min-w-0 text-lg font-semibold text-gray-800 transition group-hover:text-brand-600">{{ $category->name }}</span>
                    </div>
                    <svg class="h-5 w-5 shrink-0 text-gray-400 transition group-hover:translate-x-1 group-hover:text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            @empty
                <p class="col-span-full text-sm text-gray-500">Kategori akan segera tersedia.</p>
            @endforelse
        </div>
    </div>
</section>

{{-- ================= DAFTAR PRODUK ================= --}}
<section id="produk" class="scroll-mt-24 bg-gray-50/60">
    <div class="ss-container py-16 sm:py-20">

        <div class="mb-12 flex flex-col items-start gap-2">
            <span class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600">Katalog</span>
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Produk Pilihan</h2>
            <div class="mt-2 h-1 w-14 rounded-full bg-brand-600"></div>
            <p class="mt-3 text-sm text-gray-500 sm:text-base">Temukan favoritmu hari ini</p>
        </div>

        <div class="grid grid-cols-1 gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($products as $product)
            <article class="group flex flex-col overflow-hidden rounded-3xl border border-gray-100 bg-white shadow-sm transition-all duration-500 hover:-translate-y-1.5 hover:shadow-2xl hover:shadow-gray-300/50">

                {{-- Gambar --}}
                <a href="{{ route('products.show', $product) }}" class="relative block aspect-[4/5] overflow-hidden bg-gray-100">
                    @if ($product->primaryImage && $product->primaryImage->path)
                        <img src="{{ \Illuminate\Support\Str::startsWith($product->primaryImage->path, ['http://', 'https://']) ? $product->primaryImage->path : asset('storage/' . ltrim($product->primaryImage->path, '/')) }}"
                             alt="{{ $product->name }}" loading="lazy"
                             onerror="this.onerror=null;this.src='{{ $placeholder }}';"
                             class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-110">
                    @else
                        <img src="{{ $placeholder }}" alt="{{ $product->name }}" loading="lazy"
                             class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-110">
                    @endif

                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent opacity-0 transition-opacity duration-500 group-hover:opacity-100"></div>

                    <span class="absolute bottom-4 left-1/2 hidden -translate-x-1/2 translate-y-3 items-center gap-1.5 rounded-full bg-white/95 px-4 py-2 text-xs font-semibold text-gray-900 opacity-0 shadow-lg backdrop-blur transition-all duration-500 group-hover:translate-y-0 group-hover:opacity-100 md:inline-flex">
                        Lihat Detail
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </span>
                </a>

                {{-- Info --}}
                <div class="flex flex-1 flex-col p-5 sm:p-6">
                    <a href="{{ route('products.show', $product) }}" class="block">
                        <h3 class="line-clamp-2 min-h-[3rem] text-lg font-semibold leading-snug text-gray-900 transition-colors duration-300 group-hover:text-brand-600">
                            {{ $product->name }}
                        </h3>
                    </a>

                    <p class="mt-3 text-2xl font-bold tracking-tight text-brand-600">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </p>

                    {{-- Tombol: [ikon keranjang] [Beli Langsung mengisi sisa lebar] --}}
                    <div class="mt-auto grid grid-cols-[3rem_minmax(0,1fr)] gap-2 pt-6">

                        <form action="{{ route('cart.store') }}" method="POST" class="min-w-0">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit"
                                    title="Tambah ke Keranjang"
                                    aria-label="Tambah ke Keranjang"
                                    class="flex h-12 w-full items-center justify-center rounded-xl bg-gray-900 text-white transition duration-300 hover:bg-gray-700 hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-gray-900/20 active:scale-95">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                                </svg>
                            </button>
                        </form>

                        <form action="{{ route('cart.buyNow') }}" method="POST" class="min-w-0">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit"
                                    class="flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-sm font-semibold text-white shadow-lg shadow-brand-600/30 transition duration-300 hover:bg-brand-700 hover:shadow-xl hover:shadow-brand-600/40 focus:outline-none focus:ring-4 focus:ring-brand-600/30 active:scale-[0.98]">
                                <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                                </svg>
                                <span class="truncate">Beli Langsung</span>
                            </button>
                        </form>
                    </div>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endsection