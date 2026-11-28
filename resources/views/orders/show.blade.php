@extends('layouts.app')
@section('title', 'Detail Pesanan - StyleShop')

@section('content')
@php
    $statusClasses = match ($order->status) {
        'pending' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'paid' => 'bg-sky-50 text-sky-700 ring-sky-200',
        'processing' => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
        'shipped' => 'bg-violet-50 text-violet-700 ring-violet-200',
        'completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'cancelled' => 'bg-rose-50 text-rose-700 ring-rose-200',
        default => 'bg-gray-50 text-gray-700 ring-gray-200',
    };

    $shippingAddress = collect([
        $order->shipping_address,
        $order->village_name,
        $order->district_name,
        $order->city_name,
        $order->province_name,
        $order->postal_code ? 'Kode Pos ' . $order->postal_code : null,
    ])->filter()->implode(', ');

    $paymentMethod = match ($order->payment_method) {
        'transfer' => 'Transfer Bank',
        'cod' => 'COD (Bayar di Tempat)',
        default => ucfirst((string) $order->payment_method),
    };

    $trackingStep = match ($order->status) {
        'pending' => 1,
        'paid', 'processing' => 2,
        'shipped' => 3,
        'completed' => 4,
        default => 1,
    };

    $placeholder = 'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=400&q=80';
@endphp

<section class="bg-gradient-to-b from-brand-50 to-gray-50">
    <div class="mx-auto max-w-6xl px-4 py-10 md:py-14">
        <p class="text-sm font-semibold uppercase tracking-widest text-brand-600">Akun Saya</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 md:text-4xl">Detail Pesanan</h1>
        <p class="mt-2 text-gray-600">Informasi lengkap pesanan dan pengirimanmu.</p>
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 pt-8 md:pt-12">
    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-7">
        <h2 class="text-lg font-bold text-gray-900">Status Pelacakan</h2>

        @if ($order->status === 'cancelled')
            <div class="mt-5 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800" role="alert">
                <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18"/>
                </svg>
                <div>
                    <p class="font-semibold">Pesanan Dibatalkan</p>
                    <p class="mt-1 text-sm text-red-700">Pesanan ini tidak akan diproses lebih lanjut.</p>
                </div>
            </div>
        @else
            <div class="mt-7 grid grid-cols-4">
                @foreach (['Pesanan Dibuat', 'Diproses', 'Dikirim', 'Selesai'] as $index => $stepLabel)
                    @php
                        $stepNumber = $index + 1;
                        $stepActive = $trackingStep >= $stepNumber;
                        $finalStepComplete = $order->status === 'completed' && $stepNumber === 4;
                        $stepCircleClass = $finalStepComplete
                            ? 'bg-green-600 text-white'
                            : ($stepActive ? 'bg-brand-600 text-white' : 'bg-gray-200 text-gray-500');
                        $stepLabelClass = $stepActive ? 'text-gray-800' : 'text-gray-400';
                    @endphp
                    <div class="relative flex flex-col items-center text-center">
                        @if ($stepNumber < 4)
                            <span class="absolute left-1/2 top-5 h-1 w-full {{ $trackingStep > $stepNumber ? 'bg-brand-600' : 'bg-gray-200' }}"></span>
                        @endif
                        <span class="relative z-10 flex h-10 w-10 items-center justify-center rounded-full {{ $stepCircleClass }}">
                            @if ($stepActive)
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6"/>
                                </svg>
                            @else
                                <span class="text-sm font-semibold">{{ $stepNumber }}</span>
                            @endif
                        </span>
                        <span class="mt-3 max-w-[5.5rem] text-xs font-semibold leading-tight sm:max-w-none sm:text-sm {{ $stepLabelClass }}">
                            {{ $stepLabel }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

<section class="mx-auto max-w-6xl space-y-6 px-4 py-8 md:py-12">
    <header class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-7">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Kode Invoice</p>
                <h2 class="mt-1 break-all text-xl font-bold text-gray-900 sm:text-2xl">{{ $order->order_code }}</h2>
                <p class="mt-2 text-sm text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</p>
            </div>
            <span class="inline-flex w-fit items-center rounded-full px-3 py-1.5 text-sm font-semibold capitalize ring-1 ring-inset {{ $statusClasses }}">
                {{ $order->status }}
            </span>
        </div>
    </header>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-7">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 22l8-5V7l-8-5-8 5v10l8 5Zm0 0V12m8-5-8 5-8-5"/>
                        </svg>
                    </span>
                    <h2 class="text-lg font-bold text-gray-900">Informasi Pengiriman</h2>
                </div>

                <dl class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm text-gray-500">Nama Penerima</dt>
                        <dd class="mt-1 font-semibold text-gray-900">{{ $order->receiver_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">No. HP</dt>
                        <dd class="mt-1 font-semibold text-gray-900">{{ $order->receiver_phone }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-sm text-gray-500">Alamat Lengkap</dt>
                        <dd class="mt-1 leading-relaxed text-gray-900">{{ $shippingAddress ?: 'Alamat tidak tersedia.' }}</dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-7">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5h18M5.25 4.5h13.5A2.25 2.25 0 0121 6.75v10.5a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 17.25V6.75A2.25 2.25 0 015.25 4.5ZM7.5 15h3"/>
                        </svg>
                    </span>
                    <h2 class="text-lg font-bold text-gray-900">Metode Pembayaran</h2>
                </div>
                <p class="mt-4 font-semibold text-gray-900">{{ $paymentMethod }}</p>
            </section>

            <section class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-5 py-5 sm:px-7">
                    <h2 class="text-lg font-bold text-gray-900">Produk Pesanan</h2>
                </div>
                <div class="divide-y divide-gray-100 px-5 sm:px-7">
                    @forelse ($order->items as $item)
                        <div class="flex gap-4 py-5">
                            <div class="h-20 w-20 shrink-0 overflow-hidden rounded-xl bg-gray-100">
                                @if ($item->product?->primaryImage && $item->product->primaryImage->path)
                                    <img src="{{ \Illuminate\Support\Str::startsWith($item->product->primaryImage->path, ['http://', 'https://']) ? $item->product->primaryImage->path : asset('storage/' . ltrim($item->product->primaryImage->path, '/')) }}"
                                         alt="{{ $item->product_name }}"
                                         onerror="this.onerror=null;this.src='{{ $placeholder }}';"
                                         class="h-full w-full object-cover">
                                @else
                                    <img src="{{ $placeholder }}" alt="{{ $item->product_name }}" class="h-full w-full object-cover">
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $item->product_name }}</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}
                                </p>
                            </div>
                            <p class="shrink-0 text-right text-sm font-semibold text-gray-900">
                                Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
                            </p>
                        </div>
                    @empty
                        <p class="py-5 text-sm text-gray-500">Detail produk tidak tersedia.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <aside class="h-fit rounded-2xl border border-gray-100 bg-white p-5 shadow-sm sm:p-7">
            <h2 class="text-lg font-bold text-gray-900">Ringkasan Biaya</h2>
            <dl class="mt-5 space-y-4 border-b border-gray-100 pb-5 text-sm">
                <div class="flex items-center justify-between gap-4 text-gray-600">
                    <dt>Subtotal Produk</dt>
                    <dd class="font-medium text-gray-900">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</dd>
                </div>
                @if ($order->shipping_cost > 0)
                    <div class="flex items-center justify-between gap-4 text-gray-600">
                        <dt>Ongkos Kirim</dt>
                        <dd class="font-medium text-gray-900">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</dd>
                    </div>
                @endif
                @if ($order->discount_total > 0)
                    <div class="flex items-center justify-between gap-4 text-gray-600">
                        <dt>Diskon</dt>
                        <dd class="font-medium text-emerald-600">− Rp {{ number_format($order->discount_total, 0, ',', '.') }}</dd>
                    </div>
                @endif
            </dl>
            <div class="mt-5 flex items-center justify-between gap-4">
                <span class="font-bold text-gray-900">Total</span>
                <span class="text-lg font-bold text-brand-600">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
            </div>
        </aside>
    </div>

    <a href="{{ route('orders.index') }}"
       class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-semibold text-gray-700 shadow-sm transition hover:border-brand-600 hover:text-brand-600 focus:outline-none focus:ring-4 focus:ring-brand-100">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7 7-7M3 12h18"/>
        </svg>
        Kembali ke Pesanan Saya
    </a>
</section>
@endsection
