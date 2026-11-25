@extends('layouts.admin')
@section('title', 'Tambah Produk - Admin StyleShop')
@section('page-heading', 'Tambah Produk')
@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-6">
        <a href="{{ route('admin.products.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-700">← Kembali ke produk</a>
        <h2 class="mt-3 text-2xl font-bold tracking-tight text-slate-900">Tambah Produk Baru</h2>
        <p class="mt-1 text-sm text-slate-500">Lengkapi informasi produk dan gambar utamanya.</p>
    </div>
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
        @csrf
        @include('admin.products._form')
        <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
            <a href="{{ route('admin.products.index') }}" class="rounded-xl border border-slate-200 px-5 py-3 text-center text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</a>
            <button type="submit" class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-100">Simpan Produk</button>
        </div>
    </form>
</div>
@endsection
