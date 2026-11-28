{{-- PLACEHOLDER (backend). Variabel: $from, $to (Carbon), $report (array), $presets, $lowStockThreshold.
     $report['totals']        orders, revenue, goods, shipping, discount, average, items
     $report['daily']         tiap hari: date, orders, revenue, percent (untuk lebar batang)
     $report['top_products']  name, qty, revenue
     $report['status_counts'] jumlah pesanan yang DIBUAT pada rentang, per status
     $report['new_customers'] jumlah pelanggan baru
     $report['low_stock']     produk aktif dengan stok menipis --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Laporan Penjualan</h2>
    </x-slot>

    @php
        $t = $report['totals'];
        $rupiah = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');
        $query = ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')];
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Filter --}}
            <div class="bg-white p-5 shadow-sm sm:rounded-lg space-y-3">
                <form method="GET" class="flex flex-wrap items-end gap-3">
                    <div>
                        <label for="from" class="block text-xs text-gray-500">Dari tanggal</label>
                        <input id="from" type="date" name="from" value="{{ $from->format('Y-m-d') }}"
                               class="mt-1 border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <div>
                        <label for="to" class="block text-xs text-gray-500">Sampai tanggal</label>
                        <input id="to" type="date" name="to" value="{{ $to->format('Y-m-d') }}"
                               class="mt-1 border-gray-300 rounded-md shadow-sm text-sm">
                    </div>
                    <button class="px-4 py-2 bg-gray-800 text-white text-xs font-semibold uppercase rounded-md">Terapkan</button>
                    <a href="{{ route('admin.reports.export', $query) }}"
                       class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-semibold uppercase rounded-md hover:bg-gray-50">
                        Unduh CSV
                    </a>
                </form>

                @error('from') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                @error('to') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

                <div class="flex flex-wrap gap-2 text-xs">
                    @foreach ($presets as $label => [$presetFrom, $presetTo])
                        <a href="{{ route('admin.reports.index', ['from' => $presetFrom, 'to' => $presetTo]) }}"
                           class="px-3 py-1 rounded-full border {{ $presetFrom === $query['from'] && $presetTo === $query['to'] ? 'bg-gray-800 text-white border-gray-800' : 'bg-white text-gray-700 hover:bg-gray-50' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                <p class="text-xs text-gray-500">
                    Penjualan = pesanan yang pembayarannya berhasil, dihitung pada tanggal pembayaran.
                    Pesanan yang belum dibayar atau dibatalkan tidak masuk pendapatan.
                </p>
            </div>

            {{-- Kartu ringkasan --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-5 shadow-sm sm:rounded-lg">
                    <p class="text-xs text-gray-500">Total pendapatan</p>
                    <p class="mt-1 text-xl font-bold text-green-700">{{ $rupiah($t['revenue']) }}</p>
                    <p class="mt-1 text-xs text-gray-400">
                        Barang {{ $rupiah($t['goods']) }} &middot; Ongkir {{ $rupiah($t['shipping']) }}
                    </p>
                </div>
                <div class="bg-white p-5 shadow-sm sm:rounded-lg">
                    <p class="text-xs text-gray-500">Pesanan lunas</p>
                    <p class="mt-1 text-xl font-bold">{{ $t['orders'] }}</p>
                    <p class="mt-1 text-xs text-gray-400">{{ $t['items'] }} barang terjual</p>
                </div>
                <div class="bg-white p-5 shadow-sm sm:rounded-lg">
                    <p class="text-xs text-gray-500">Rata-rata per pesanan</p>
                    <p class="mt-1 text-xl font-bold">{{ $rupiah($t['average']) }}</p>
                </div>
                <div class="bg-white p-5 shadow-sm sm:rounded-lg">
                    <p class="text-xs text-gray-500">Pelanggan baru</p>
                    <p class="mt-1 text-xl font-bold">{{ $report['new_customers'] }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Penjualan per hari --}}
                <section class="lg:col-span-2 bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="font-semibold mb-3">Penjualan per Hari</h3>

                    @if ($t['orders'] === 0)
                        <p class="text-sm text-gray-500">Belum ada penjualan pada rentang ini.</p>
                    @else
                        <div class="max-h-96 overflow-y-auto">
                            <table class="min-w-full text-sm">
                                <thead class="text-left text-gray-600 sticky top-0 bg-white">
                                    <tr>
                                        <th class="py-2 pr-4">Tanggal</th>
                                        <th class="py-2 pr-4">Pesanan</th>
                                        <th class="py-2 pr-4">Pendapatan</th>
                                        <th class="py-2 w-1/3"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($report['daily'] as $day)
                                        <tr>
                                            <td class="py-2 pr-4 whitespace-nowrap">{{ $day['date']->format('d M Y') }}</td>
                                            <td class="py-2 pr-4">{{ $day['orders'] }}</td>
                                            <td class="py-2 pr-4 whitespace-nowrap">{{ $rupiah($day['revenue']) }}</td>
                                            <td class="py-2">
                                                @if ($day['percent'] > 0)
                                                    <div class="h-2 rounded bg-indigo-500" style="width: {{ $day['percent'] }}%"></div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>

                {{-- Status pesanan yang dibuat --}}
                <section class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="font-semibold mb-1">Pesanan Dibuat</h3>
                    <p class="text-xs text-gray-400 mb-3">Berdasarkan tanggal pesanan dibuat, per status saat ini.</p>
                    <ul class="space-y-2 text-sm">
                        @foreach ($report['status_counts'] as $status => $count)
                            <li class="flex items-center justify-between">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold {{ \App\Support\OrderStatus::badge($status) }}">
                                    {{ \App\Support\OrderStatus::label($status) }}
                                </span>
                                <span class="font-medium">{{ $count }}</span>
                            </li>
                        @endforeach
                    </ul>
                </section>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- Produk terlaris --}}
                <section class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="font-semibold mb-3">Produk Terlaris</h3>

                    @if (empty($report['top_products']))
                        <p class="text-sm text-gray-500">Belum ada produk terjual pada rentang ini.</p>
                    @else
                        <table class="min-w-full text-sm">
                            <thead class="text-left text-gray-600">
                                <tr>
                                    <th class="py-2 pr-4">#</th>
                                    <th class="py-2 pr-4">Produk</th>
                                    <th class="py-2 pr-4">Terjual</th>
                                    <th class="py-2">Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($report['top_products'] as $i => $product)
                                    <tr>
                                        <td class="py-2 pr-4">{{ $i + 1 }}</td>
                                        <td class="py-2 pr-4">{{ $product['name'] }}</td>
                                        <td class="py-2 pr-4">{{ $product['qty'] }}</td>
                                        <td class="py-2 whitespace-nowrap">{{ $rupiah($product['revenue']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </section>

                {{-- Stok menipis --}}
                <section class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="font-semibold mb-1">Stok Menipis</h3>
                    <p class="text-xs text-gray-400 mb-3">Produk aktif dengan stok {{ $lowStockThreshold }} atau kurang (tidak terpengaruh filter tanggal).</p>

                    @if ($report['low_stock']->isEmpty())
                        <p class="text-sm text-gray-500">Semua stok aman.</p>
                    @else
                        <ul class="divide-y divide-gray-100 text-sm">
                            @foreach ($report['low_stock'] as $product)
                                <li class="py-2 flex items-center justify-between">
                                    <a href="{{ route('admin.products.edit', $product->id) }}"
                                       class="text-indigo-600 hover:underline">{{ $product->name }}</a>
                                    <span class="font-semibold {{ $product->stock === 0 ? 'text-red-600' : 'text-yellow-700' }}">
                                        {{ $product->stock === 0 ? 'Habis' : 'Sisa ' . $product->stock }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
