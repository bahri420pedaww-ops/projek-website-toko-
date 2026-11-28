@extends('layouts.admin')
@section('title', 'Pesanan - Admin StyleShop')
@section('page-heading', 'Pesanan')

@section('content')
<div class="mx-auto max-w-7xl">
    <div class="mb-6">
        <p class="text-sm text-slate-500">Pantau transaksi dan perbarui status pemenuhan.</p>
        <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Manajemen Pesanan</h2>
    </div>

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-5 py-4 font-semibold">Kode Order</th>
                        <th class="px-5 py-4 font-semibold">Nama Pembeli</th>
                        <th class="px-5 py-4 font-semibold">Total</th>
                        <th class="px-5 py-4 font-semibold">Status</th>
                        <th class="px-5 py-4 font-semibold">Tanggal</th>
                        <th class="px-5 py-4 font-semibold">Ubah Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($orders as $order)
                        @php
                            $statusClasses = [
                                'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                'processing' => 'bg-blue-100 text-blue-800 border-blue-200',
                                'shipped' => 'bg-purple-100 text-purple-800 border-purple-200',
                                'completed' => 'bg-green-100 text-green-800 border-green-200',
                                'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                            ];
                            $badgeClass = $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                        @endphp
                        <tr class="align-top hover:bg-slate-50/70">
                            <td class="whitespace-nowrap px-5 py-5 font-semibold text-slate-900">{{ $order->order_code }}</td>
                            <td class="px-5 py-5 text-slate-700">{{ $order->user?->name ?? 'Pelanggan' }}</td>
                            <td class="whitespace-nowrap px-5 py-5 font-medium text-slate-800">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                            <td class="px-5 py-5">
                                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $badgeClass }}">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-5 text-slate-500">{{ $order->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-5 py-4">
                                <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="flex min-w-[270px] gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <label class="sr-only" for="status-{{ $order->id }}">Status pesanan</label>
                                    <select id="status-{{ $order->id }}" name="status" required class="min-w-0 flex-1 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                                        <option value="" disabled @selected(!in_array($order->status, $statuses, true))>Pilih status</option>
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="note" value="Status diperbarui oleh admin">
                                    <button type="submit" class="rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-indigo-700">Simpan</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-14 text-center text-slate-500">Belum ada pesanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($orders->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">{{ $orders->links() }}</div>
        @endif
    </div>
</div>
@endsection
