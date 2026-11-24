{{-- PLACEHOLDER (backend). Variabel: $orders (paginator).
     Tiap pesanan: order_code, created_at, status, grand_total, items_count.
     Label dan warna status: \App\Support\OrderStatus::label($status) / ::badge($status) --}}
@extends('shop.layout')

@section('title', 'Pesanan Saya')

@section('content')
    <h1 class="text-2xl font-semibold mb-6">Pesanan Saya</h1>

    @if ($orders->isEmpty())
        <div class="bg-white p-8 rounded-lg shadow-sm text-center text-gray-600">
            Kamu belum memiliki pesanan.
            <a href="{{ route('shop.products.index') }}" class="text-indigo-600 hover:underline">Mulai belanja</a>
        </div>
    @else
        <div class="bg-white shadow-sm rounded-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Barang</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($orders as $order)
                        <tr>
                            <td class="px-4 py-3">
                                <a href="{{ route('orders.show', $order->order_code) }}"
                                   class="text-indigo-600 hover:underline">{{ $order->order_code }}</a>
                            </td>
                            <td class="px-4 py-3">{{ $order->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3">{{ $order->items_count }} produk</td>
                            <td class="px-4 py-3">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold {{ \App\Support\OrderStatus::badge($order->status) }}">
                                    {{ \App\Support\OrderStatus::label($order->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $orders->links() }}</div>
    @endif
@endsection
