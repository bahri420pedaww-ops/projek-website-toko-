@extends('layouts.app')
@section('title', 'Masuk - StyleShop')
@section('content')
<div class="mx-auto max-w-md px-4 py-14 sm:py-20">
    <div class="mb-7 text-center">
        <p class="text-sm font-semibold uppercase tracking-widest text-brand-600">StyleShop</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-900">Masuk ke akunmu</h1>
        <p class="mt-2 text-sm text-gray-500">Masuk untuk melanjutkan belanja.</p>
    </div>
    <form action="{{ route('login.store') }}" method="POST" class="space-y-5 rounded-2xl border border-gray-100 bg-white p-6 shadow-lg shadow-gray-200/50 sm:p-8">
        @csrf
        @if ($errors->any())
            <div class="rounded-xl border border-red-100 bg-red-50 p-3 text-sm text-red-700" role="alert">{{ $errors->first() }}</div>
        @endif
        <label for="email" class="block text-sm font-semibold text-gray-700">Email
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-3 outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-brand-100">
        </label>
        <label for="password" class="block text-sm font-semibold text-gray-700">Kata sandi
            <input id="password" type="password" name="password" required autocomplete="current-password" class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-3 outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-brand-100">
        </label>
        <label class="flex items-center gap-2 text-sm text-gray-600">
            <input type="checkbox" name="remember" value="1" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
            Ingat saya
        </label>
        <button type="submit" class="w-full rounded-xl bg-brand-600 py-3.5 font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-4 focus:ring-brand-100">Masuk</button>
        <p class="text-center text-sm text-gray-500">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-semibold text-brand-600 hover:text-brand-700">Daftar di sini</a>
        </p>
    </form>
</div>
@endsection
