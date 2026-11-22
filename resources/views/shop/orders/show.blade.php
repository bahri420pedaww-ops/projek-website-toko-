{{-- PLACEHOLDER (backend). Variabel: $order dengan relasi items, histories, shippingMethod, shipment.
     order: order_code, status, created_at, receiver_name, receiver_phone, shipping_address,
            city_name, province_name, postal_code, total_weight_gram, subtotal, shipping_cost,
            shipping_etd (bisa null), discount_total, grand_total, note
     items: product_name, price, quantity, subtotal (dihitung)
     histories: status, note, created_at
     shipment: tracking_number, shipped_at, delivered_at (bisa null) --}}
@extends('shop.layout')

@section('title', 'Pesanan ' . $order->order_code)

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-semibold">{{ $order->order_code }}</h1>
            <p class="text-sm text-gray-500">Dibuat {{ $order->created_at->format('d M Y H:i') }}</p>
        </div>
        <span class="px-3 py-1 rounded text-sm font-semibold {{ \App\Support\OrderStatus::badge($order->status) }}">
            {{ \App\Support\OrderStatus::label($order->status) }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">

            {{-- Isi pesanan --}}
            <section class="bg-white shadow-sm rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3">Harga</th>
                            <th class="px-4 py-3">Jumlah</th>
                            <th class="px-4 py-3">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="px-4 py-3">{{ $item->product_name }}</td>
                                <td class="px-4 py-3">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">{{ $item->quantity }}</td>
                                <td class="px-4 py-3">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </section>

            {{-- Pengiriman --}}
            <section class="bg-white p-6 rounded-lg shadow-sm text-sm">
                <h2 class="font-semibold mb-2">Pengiriman</h2>
                <p class="font-medium">{{ $order->receiver_name }} <span class="text-gray-600">({{ $order->receiver_phone }})</span></p>
                <p class="text-gray-600">
                    {{ $order->shipping_address }}, {{ $order->city_name }}, {{ $order->province_name }}
                    {{ $order->postal_code }}
                </p>
                <p class="mt-2 text-gray-600">
                    Layanan: {{ $order->shippingMethod->courier }} {{ $order->shippingMethod->service }}
                    @if ($order->shipping_etd)
                        &middot; Estimasi {{ $order->shipping_etd }}
                    @endif
                </p>
                @if ($order->total_weight_gram > 0)
                    <p class="text-gray-600">Berat: {{ number_format($order->total_weight_gram, 0, ',', '.') }} gram</p>
                @endif
                @if ($order->shipment?->tracking_number)
                    <p class="text-gray-600">No. resi: <span class="font-medium">{{ $order->shipment->tracking_number }}</span></p>
                @endif
                @if ($order->note)
                    <p class="mt-2 text-gray-600">Catatan: {{ $order->note }}</p>
                @endif
            </section>

            {{-- Riwayat status --}}
            <section class="bg-white p-6 rounded-lg shadow-sm">
                <h2 class="font-semibold mb-3">Riwayat Status</h2>
                <ol class="space-y-3 text-sm">
                    @foreach ($order->histories->reverse() as $history)
                        <li>
                            <p class="font-medium">{{ \App\Support\OrderStatus::label($history->status) }}</p>
                            @if ($history->note)
                                <p class="text-gray-600">{{ $history->note }}</p>
                            @endif
                            <p class="text-xs text-gray-400">{{ $history->created_at?->format('d M Y H:i') }}</p>
                        </li>
                    @endforeach
                </ol>
            </section>
        </div>

        {{-- Ringkasan biaya dan pembayaran --}}
        <aside>
            <div class="bg-white p-6 rounded-lg shadow-sm space-y-2 text-sm">
                <h2 class="font-semibold mb-1">Rincian Biaya</h2>
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal</span>
                    <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Ongkos kirim</span>
                    <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                </div>
                @if ($order->discount_total > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Diskon</span>
                        <span>- Rp {{ number_format($order->discount_total, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between font-semibold border-t pt-3">
                    <span>Total</span>
                    <span>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                </div>

                @if ($order->status === 'pending')
                    {{-- Tombol ini diaktifkan di langkah Payment Gateway --}}
                    <button type="button" disabled
                            class="mt-4 w-full px-4 py-3 bg-gray-300 text-gray-600 text-sm font-semibold rounded-md cursor-not-allowed">
                        Bayar sekarang (segera hadir)
                    </button>
                @endif
            </div>
        </aside>
    </div>
@endsection
