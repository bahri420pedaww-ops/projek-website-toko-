{{-- PLACEHOLDER (backend). Variabel: $order dengan relasi user, items, shippingMethod, shipment,
     payments, histories (tiap riwayat punya changedBy, bisa null = sistem).
     Aksi (semua POST dengan @csrf): admin.orders.process | ship (field tracking_number) |
     complete | cancel --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pesanan {{ $order->order_code }}</h2>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-600 hover:underline">&larr; Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('admin._flash')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">

                    {{-- Isi pesanan --}}
                    <section class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
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

                    {{-- Pembeli dan pengiriman --}}
                    <section class="bg-white p-6 shadow-sm sm:rounded-lg text-sm space-y-3">
                        <div>
                            <h3 class="font-semibold mb-1">Pembeli</h3>
                            @if ($order->user)
                                <p>
                                    <a href="{{ route('admin.customers.show', $order->user) }}"
                                       class="font-medium text-indigo-600 hover:underline">{{ $order->user->name }}</a>
                                    <span class="text-gray-600">({{ $order->user->email }})</span>
                                </p>
                            @else
                                <p class="text-gray-500">-</p>
                            @endif
                        </div>

                        <div>
                            <h3 class="font-semibold mb-1">Dikirim ke</h3>
                            <p class="font-medium">{{ $order->receiver_name }}
                                <span class="text-gray-600">({{ $order->receiver_phone }})</span></p>
                            <p class="text-gray-600">
                                {{ $order->shipping_address }}, {{ $order->city_name }}, {{ $order->province_name }}
                                {{ $order->postal_code }}
                            </p>
                            <p class="mt-1 text-gray-600">
                                {{ $order->shippingMethod->courier }} {{ $order->shippingMethod->service }}
                                @if ($order->shipping_etd) &middot; Estimasi {{ $order->shipping_etd }} @endif
                                @if ($order->total_weight_gram > 0)
                                    &middot; {{ number_format($order->total_weight_gram, 0, ',', '.') }} gram
                                @endif
                            </p>
                            @if ($order->shipment?->tracking_number)
                                <p class="text-gray-600">No. resi:
                                    <span class="font-medium">{{ $order->shipment->tracking_number }}</span></p>
                            @endif
                            @if ($order->note)
                                <p class="mt-1 text-gray-600">Catatan pembeli: {{ $order->note }}</p>
                            @endif
                        </div>
                    </section>

                    {{-- Pembayaran --}}
                    <section class="bg-white p-6 shadow-sm sm:rounded-lg text-sm">
                        <h3 class="font-semibold mb-3">Pembayaran</h3>
                        @if ($order->payments->isEmpty())
                            <p class="text-gray-500">Belum ada percobaan pembayaran.</p>
                        @else
                            <ul class="space-y-2">
                                @foreach ($order->payments->sortByDesc('id') as $payment)
                                    <li class="flex flex-wrap items-center justify-between gap-2">
                                        <div>
                                            <p class="font-medium">{{ $payment->gateway_order_id }}</p>
                                            <p class="text-xs text-gray-500">
                                                @if ($payment->payment_type)
                                                    {{ str_replace('_', ' ', $payment->payment_type) }} &middot;
                                                @endif
                                                {{ $payment->paid_at
                                                    ? 'Dibayar ' . $payment->paid_at->format('d M Y H:i')
                                                    : 'Dibuat ' . $payment->created_at->format('d M Y H:i') }}
                                            </p>
                                        </div>
                                        <span class="px-2 py-0.5 rounded text-xs font-semibold {{ \App\Support\PaymentStatus::badge($payment->status) }}">
                                            {{ \App\Support\PaymentStatus::label($payment->status) }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </section>

                    {{-- Riwayat status --}}
                    <section class="bg-white p-6 shadow-sm sm:rounded-lg">
                        <h3 class="font-semibold mb-3">Riwayat Status</h3>
                        <ol class="space-y-3 text-sm">
                            @foreach ($order->histories->reverse() as $history)
                                <li>
                                    <p class="font-medium">{{ \App\Support\OrderStatus::label($history->status) }}</p>
                                    @if ($history->note)
                                        <p class="text-gray-600">{{ $history->note }}</p>
                                    @endif
                                    <p class="text-xs text-gray-400">
                                        {{ $history->created_at?->format('d M Y H:i') }}
                                        &middot; oleh {{ $history->changedBy?->name ?? 'sistem' }}
                                    </p>
                                </li>
                            @endforeach
                        </ol>
                    </section>
                </div>

                {{-- Kolom kanan: status, biaya, aksi --}}
                <aside class="space-y-6">
                    <div class="bg-white p-6 shadow-sm sm:rounded-lg space-y-2 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Status</span>
                            <span class="px-2 py-0.5 rounded text-xs font-semibold {{ \App\Support\OrderStatus::badge($order->status) }}">
                                {{ \App\Support\OrderStatus::label($order->status) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-400">Dibuat {{ $order->created_at->format('d M Y H:i') }}</p>

                        <div class="border-t pt-3 space-y-1">
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
                            <div class="flex justify-between font-semibold border-t pt-2">
                                <span>Total</span>
                                <span>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Aksi sesuai status --}}
                    <div class="bg-white p-6 shadow-sm sm:rounded-lg space-y-3 text-sm">
                        <h3 class="font-semibold">Tindakan</h3>

                        @if ($order->status === 'pending')
                            <p class="text-gray-600">Menunggu pembayaran dari pembeli.</p>
                            <form method="POST" action="{{ route('admin.orders.cancel', $order) }}"
                                  onsubmit="return confirm('Batalkan pesanan ini? Stok akan dikembalikan.')">
                                @csrf
                                <button class="w-full px-4 py-2 border border-red-300 text-red-700 font-medium rounded-md hover:bg-red-50">
                                    Batalkan pesanan
                                </button>
                            </form>

                        @elseif ($order->status === 'paid')
                            <p class="text-gray-600">Pembayaran sudah diterima. Siapkan barangnya.</p>
                            <form method="POST" action="{{ route('admin.orders.process', $order) }}">
                                @csrf
                                <button class="w-full px-4 py-2 bg-gray-800 text-white font-semibold rounded-md hover:bg-gray-700">
                                    Proses pesanan
                                </button>
                            </form>

                        @elseif ($order->status === 'processing')
                            <p class="text-gray-600">Masukkan nomor resi lalu tandai dikirim.</p>
                            <form method="POST" action="{{ route('admin.orders.ship', $order) }}" class="space-y-2">
                                @csrf
                                <input type="text" name="tracking_number" maxlength="100"
                                       value="{{ old('tracking_number') }}" placeholder="Nomor resi"
                                       class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                @error('tracking_number') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                                <button class="w-full px-4 py-2 bg-gray-800 text-white font-semibold rounded-md hover:bg-gray-700">
                                    Kirim pesanan
                                </button>
                            </form>

                        @elseif ($order->status === 'shipped')
                            <p class="text-gray-600">Pesanan sedang dalam pengiriman.</p>
                            <form method="POST" action="{{ route('admin.orders.complete', $order) }}"
                                  onsubmit="return confirm('Tandai pesanan ini selesai?')">
                                @csrf
                                <button class="w-full px-4 py-2 bg-gray-800 text-white font-semibold rounded-md hover:bg-gray-700">
                                    Tandai selesai
                                </button>
                            </form>

                        @elseif ($order->status === 'completed')
                            <p class="text-green-700">Pesanan sudah selesai.</p>

                        @else
                            <p class="text-gray-600">Pesanan dibatalkan.</p>
                        @endif
                    </div>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
