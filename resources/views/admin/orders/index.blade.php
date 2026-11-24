{{-- PLACEHOLDER (backend). Variabel: $orders (paginator), $status (filter aktif atau null),
     $keyword, $counts (jumlah pesanan per status).
     Tiap pesanan: order_code, created_at, receiver_name, grand_total, status, items_count, user --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pesanan</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @include('admin._flash')

            {{-- Tab status --}}
            <div class="mb-4 flex flex-wrap gap-2 text-sm">
                <a href="{{ route('admin.orders.index', array_filter(['q' => $keyword])) }}"
                   class="px-3 py-1 rounded-full border {{ $status === null ? 'bg-gray-800 text-white border-gray-800' : 'bg-white text-gray-700' }}">
                    Semua ({{ $counts->sum() }})
                </a>
                @foreach (\App\Support\OrderStatus::LABELS as $key => $label)
                    <a href="{{ route('admin.orders.index', array_filter(['status' => $key, 'q' => $keyword])) }}"
                       class="px-3 py-1 rounded-full border {{ $status === $key ? 'bg-gray-800 text-white border-gray-800' : 'bg-white text-gray-700' }}">
                        {{ $label }} ({{ $counts[$key] ?? 0 }})
                    </a>
                @endforeach
            </div>

            <form method="GET" class="mb-4 flex flex-wrap gap-2">
                @if ($status)
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif
                <input type="text" name="q" value="{{ $keyword }}" placeholder="Cari kode, penerima, nama atau email pembeli"
                       class="border-gray-300 rounded-md shadow-sm text-sm w-80">
                <button class="px-4 py-2 bg-gray-800 text-white text-xs font-semibold uppercase rounded-md">Cari</button>
            </form>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Kode</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Pembeli</th>
                            <th class="px-4 py-3">Penerima</th>
                            <th class="px-4 py-3">Barang</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($orders as $order)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $order->order_code }}</td>
                                <td class="px-4 py-3">{{ $order->created_at->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3">{{ $order->user?->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $order->receiver_name }}</td>
                                <td class="px-4 py-3">{{ $order->items_count }}</td>
                                <td class="px-4 py-3">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded text-xs font-semibold {{ \App\Support\OrderStatus::badge($order->status) }}">
                                        {{ \App\Support\OrderStatus::label($order->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                       class="text-indigo-600 hover:underline">Lihat</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-gray-500">Tidak ada pesanan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $orders->links() }}</div>
        </div>
    </div>
</x-app-layout>
