@extends('layouts.app')
@section('title', 'Daftar - StyleShop')

@section('content')
<div class="mx-auto max-w-md px-4 py-14 sm:py-20">
    <div class="mb-7 text-center">
        <p class="text-sm font-semibold uppercase tracking-widest text-brand-600">StyleShop</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-900">Buat akun baru</h1>
        <p class="mt-2 text-sm text-gray-500">Daftar untuk mulai berbelanja bersama kami.</p>
    </div>

    <form action="{{ route('register.store') }}" method="POST" class="space-y-5 rounded-2xl border border-gray-100 bg-white p-6 shadow-lg shadow-gray-200/50 sm:p-8">
        @csrf
        @if ($errors->any())
            <div class="rounded-xl border border-red-100 bg-red-50 p-3 text-sm text-red-700" role="alert">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required maxlength="255" autocomplete="name"
                   class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-3 outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-brand-100">
        </div>

        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email"
                   class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-3 outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-brand-100">
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700">Password</label>
            <input id="password" type="password" name="password" required minlength="8" autocomplete="new-password"
                   class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-3 outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-brand-100">
            <p class="mt-1 text-xs text-gray-400">Gunakan minimal 8 karakter.</p>
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password"
                   class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-3 outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-brand-100">
        </div>

        <button type="submit" class="w-full rounded-xl bg-brand-600 py-3.5 font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-4 focus:ring-brand-100">
            Daftar
        </button>
        <p class="text-center text-sm text-gray-500">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-semibold text-brand-600 hover:text-brand-700">Masuk di sini</a>
        </p>
    </form>
</div>
@endsection
