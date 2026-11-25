@extends('layouts.admin')
@section('title', 'Dashboard Admin - StyleShop')
@section('page-heading', 'Dashboard')

@section('content')
<div class="mx-auto max-w-7xl space-y-8">
    <div>
        <p class="text-sm text-slate-500">Ringkasan performa toko</p>
        <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Selamat datang kembali</h2>
    </div>

    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Produk</p>
                    <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalProducts, 0, ',', '.') }}</p>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 text-lg text-indigo-600" aria-hidden="true">▣</span>
            </div>
            <a href="{{ route('admin.products.index') }}" class="mt-5 inline-flex text-sm font-semibold text-indigo-600 hover:text-indigo-700">Kelola produk <span class="ml-1">→</span></a>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Pesanan</p>
                    <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">{{ number_format($totalOrders, 0, ',', '.') }}</p>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-lg text-amber-600" aria-hidden="true">▤</span>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="mt-5 inline-flex text-sm font-semibold text-indigo-600 hover:text-indigo-700">Lihat pesanan <span class="ml-1">→</span></a>
        </article>

        <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:col-span-2 xl:col-span-1">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Pendapatan</p>
                    <p class="mt-3 text-2xl font-bold tracking-tight text-slate-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                </div>
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-lg text-emerald-600" aria-hidden="true">Rp</span>
            </div>
            <p class="mt-5 text-xs text-slate-400">Dari pesanan yang telah dibayar atau diproses.</p>
        </article>
    </div>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-5 py-5 sm:px-6">
            <div>
                <h3 class="font-bold text-slate-900">Pesanan Terbaru</h3>
                <p class="mt-1 text-sm text-slate-500">Lima aktivitas pesanan terakhir di toko.</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="shrink-0 text-sm font-semibold text-indigo-600 hover:text-indigo-700">Semua pesanan →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-3 font-semibold">Kode Order</th>
                        <th class="px-6 py-3 font-semibold">Pembeli</th>
                        <th class="px-6 py-3 font-semibold">Tanggal</th>
                        <th class="px-6 py-3 font-semibold">Status</th>
                        <th class="px-6 py-3 text-right font-semibold">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($latestOrders as $order)
                        <tr class="hover:bg-slate-50/70">
                            <td class="whitespace-nowrap px-6 py-4 font-semibold text-slate-900">{{ $order->order_code }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $order->user?->name ?? 'Pelanggan' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-slate-500">{{ $order->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-6 py-4">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right font-semibold text-slate-900">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-slate-500">Belum ada pesanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
