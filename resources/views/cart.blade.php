@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">

    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Keranjang Belanja</h1>
        <p class="mt-1 text-sm text-gray-500">Periksa kembali pesananmu sebelum checkout</p>
    </div>

    @if (count($items))
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">

        {{-- DAFTAR ITEM --}}
        <div class="space-y-4 lg:col-span-2">
            @foreach ($items as $item)
            <div class="flex gap-4 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm sm:gap-6 sm:p-5">

                <div class="h-24 w-24 shrink-0 overflow-hidden rounded-xl bg-gray-100 sm:h-32 sm:w-32">
                    <img src="{{ asset('storage/' . $item->product->image) }}"
                         alt="{{ $item->product->name }}"
                         class="h-full w-full object-cover">
                </div>

                <div class="flex min-w-0 flex-1 flex-col">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="line-clamp-2 text-base font-semibold text-gray-900 sm:text-lg">{{ $item->product->name }}</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Rp {{ number_format($item->product->price, 0, ',', '.') }} / pcs
                            </p>
                        </div>

                        {{-- Hapus --}}
                        <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Hapus item" aria-label="Hapus item"
                                    class="rounded-lg p-2 text-gray-400 transition hover:bg-red-50 hover:text-red-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </form>
                    </div>

                    <div class="mt-auto flex flex-wrap items-end justify-between gap-3 pt-4">
                        {{-- Ubah jumlah --}}
                        <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                   class="h-10 w-20 rounded-xl border border-gray-200 bg-white text-center text-sm font-semibold text-gray-900 shadow-sm focus:border-brand-600 focus:outline-none focus:ring-4 focus:ring-brand-600/10">
                            <button type="submit"
                                    class="h-10 rounded-xl border border-gray-200 px-3 text-sm font-medium text-gray-700 transition hover:border-gray-900 hover:text-gray-900">
                                Perbarui
                            </button>
                        </form>

                        <p class="text-lg font-bold text-gray-900">
                            Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
            @endforeach

            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 pt-2 text-sm font-medium text-gray-600 transition hover:text-brand-600">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                Lanjut Belanja
            </a>
        </div>

        {{-- RINGKASAN PESANAN --}}
        <aside class="lg:col-span-1">
            <div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-xl shadow-gray-200/60 sm:p-8 lg:sticky lg:top-24">
                <h2 class="text-lg font-bold text-gray-900">Ringkasan Pesanan</h2>

                <dl class="mt-6 space-y-4 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-500">Subtotal</dt>
                        <dd class="font-semibold text-gray-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-gray-500">Ongkos kirim</dt>
                        <dd class="text-gray-500">Dihitung saat checkout</dd>
                    </div>
                </dl>

                <div class="my-6 h-px bg-gray-100"></div>

                <div class="flex items-center justify-between">
                    <span class="text-base font-semibold text-gray-900">Total</span>
                    <span class="text-2xl font-bold text-brand-600">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>

                <a href="{{ route('checkout.index') }}"
                   class="mt-8 flex h-14 w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-6 text-base font-bold text-white shadow-lg shadow-brand-600/30 transition hover:bg-brand-700 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-brand-600/30 active:scale-[0.98]">
                    Lanjut ke Checkout
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>

                <p class="mt-4 flex items-center justify-center gap-1.5 text-xs text-gray-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                    Pembayaran aman &amp; terlindungi
                </p>
            </div>
        </aside>
    </div>

    @else
    {{-- KERANJANG KOSONG --}}
    <div class="rounded-3xl border border-dashed border-gray-200 bg-gray-50 px-6 py-20 text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-sm">
            <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
        </div>
        <h2 class="mt-6 text-xl font-bold text-gray-900">Keranjangmu masih kosong</h2>
        <p class="mt-2 text-sm text-gray-500">Yuk, temukan pakaian favoritmu di StyleShop.</p>
        <a href="{{ url('/') }}" class="mt-6 inline-flex h-12 items-center rounded-xl bg-brand-600 px-6 text-sm font-semibold text-white shadow-md shadow-brand-600/25 transition hover:bg-brand-700">
            Mulai Belanja
        </a>
    </div>
    @endif
</div>
@endsection