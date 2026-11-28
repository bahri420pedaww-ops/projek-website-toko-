@extends('layouts.app')
@section('title', 'Pesanan Saya - StyleShop')

@section('content')
<section class="bg-gradient-to-b from-brand-50 to-gray-50">
    <div class="mx-auto max-w-6xl px-4 py-10 md:py-14">
        <p class="text-sm font-semibold uppercase tracking-widest text-brand-600">Akun Saya</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 md:text-4xl">Pesanan Saya</h1>
        <p class="mt-2 text-gray-600">Lihat daftar pesanan dan barang yang sudah kamu pesan.</p>
    </div>
</section>

<section class="mx-auto max-w-6xl space-y-5 px-4 py-8 md:py-12">
    @forelse ($orders as $order)
        @php
            $statusColors = [
                'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                'processing' => 'bg-blue-100 text-blue-800 border-blue-200',
                'shipped' => 'bg-purple-100 text-purple-800 border-purple-200',
                'completed' => 'bg-green-100 text-green-800 border-green-200',
                'cancelled' => 'bg-red-100 text-red-800 border-red-200',
            ];
            $colorClass = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
        @endphp

        <article class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
            <header class="flex flex-col gap-4 border-b border-gray-100 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div class="min-w-0">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Kode Order</p>
                    <h2 class="mt-1 truncate font-bold text-gray-900">{{ $order->order_code }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div class="flex items-center justify-between gap-4 sm:justify-end">
                    <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold capitalize {{ $colorClass }}">
                        {{ $order->status }}
                    </span>
                    <div class="text-right">
                        <p class="text-xs text-gray-400">Total pesanan</p>
                        <p class="mt-1 font-bold text-brand-600">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</p>
                    </div>
                </div>
            </header>

            <div class="divide-y divide-gray-100 px-5 sm:px-6">
                @forelse ($order->items as $item)
                    <div class="flex items-center justify-between gap-4 py-4">
                        <div class="min-w-0">
                            <p class="truncate font-medium text-gray-800">
                                {{ $item->product?->name ?? $item->product_name }}
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}
                            </p>
                        </div>
                        <p class="shrink-0 text-sm font-semibold text-gray-700">
                            Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                        </p>
                    </div>
                @empty
                    <p class="py-4 text-sm text-gray-500">Detail barang pesanan tidak tersedia.</p>
                @endforelse
            </div>

            <footer class="flex justify-end border-t border-gray-100 px-5 py-4 sm:px-6">
                <a href="{{ route('orders.show', $order->id) }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-700 focus:outline-none focus:ring-4 focus:ring-brand-100">
                    Lihat Detail
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </footer>
        </article>
    @empty
        <div class="rounded-3xl border border-gray-100 bg-white px-6 py-14 text-center shadow-sm">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-brand-50 text-brand-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 4h10l2 3v13H5V7l2-3Zm-2 3h14m-9 4h4" />
                </svg>
            </div>
            <h2 class="mt-5 text-xl font-bold text-gray-900">Belum ada pesanan</h2>
            <p class="mt-2 text-gray-500">Pesanan yang kamu buat akan tampil di halaman ini.</p>
            <a href="{{ route('products.index') }}" class="mt-6 inline-flex items-center justify-center rounded-xl bg-brand-600 px-6 py-3 font-semibold text-white transition hover:bg-brand-700">
                Mulai Belanja
            </a>
        </div>
    @endforelse
</section>
@endsection