@extends('layouts.admin')
@section('title', 'Pelanggan - Admin StyleShop')
@section('page-heading', 'Pelanggan')

@section('content')
<div class="mx-auto max-w-7xl">
    <div class="mb-6">
        <p class="text-sm text-slate-500">Daftar akun pembeli yang terdaftar di toko.</p>
        <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Pelanggan</h2>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="border-b border-gray-100 bg-slate-50 text-xs uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-4 font-semibold">Nama Lengkap</th>
                        <th class="px-6 py-4 font-semibold">Email</th>
                        <th class="px-6 py-4 font-semibold">No. HP</th>
                        <th class="px-6 py-4 font-semibold">Tanggal Bergabung</th>
                        <th class="px-6 py-4 text-right font-semibold">Total Pesanan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($customers as $customer)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-sm font-bold uppercase text-indigo-600">
                                        {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($customer->name ?: 'P', 0, 1)) }}
                                    </span>
                                    <span class="font-semibold text-slate-900">{{ $customer->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $customer->email }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $customer->phone ?: '—' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-slate-600">{{ $customer->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <span class="inline-flex min-w-8 items-center justify-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                    {{ number_format($customer->orders_count) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-14 text-center">
                                <p class="font-semibold text-slate-700">Belum ada pelanggan</p>
                                <p class="mt-1 text-sm text-slate-500">Akun customer yang mendaftar akan tampil di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($customers->hasPages())
            <div class="border-t border-gray-100 px-6 py-4">{{ $customers->links() }}</div>
        @endif
    </div>
</div>
@endsection
