@extends('layouts.app')
@section('title', 'Profil Saya - StyleShop')

@section('content')
<section class="bg-gradient-to-b from-brand-50 to-gray-50">
    <div class="mx-auto max-w-5xl px-4 py-10 md:py-14">
        <p class="text-sm font-semibold uppercase tracking-widest text-brand-600">Akun Saya</p>
        <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-900 md:text-4xl">Profil</h1>
        <p class="mt-2 text-gray-600">Perbarui nama dan kata sandi akunmu.</p>
    </div>
</section>

<section class="mx-auto max-w-5xl px-4 py-8 md:py-12">
    <div class="mx-auto max-w-2xl">
        @if (session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800" role="status">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert">
                <p class="font-semibold">Periksa kembali informasi yang kamu masukkan.</p>
                <ul class="mt-2 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST"
              class="space-y-5 rounded-2xl border border-gray-100 bg-white p-6 shadow-lg shadow-gray-200/50 sm:p-8">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700">Nama Lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                       required maxlength="255" autocomplete="name"
                       class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-brand-100">
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700">Email</label>
                <input id="email" type="email" value="{{ auth()->user()->email }}" readonly autocomplete="email"
                       class="mt-2 w-full cursor-not-allowed rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-500 outline-none">
                <p class="mt-1 text-xs text-gray-400">Email tidak dapat diubah.</p>
            </div>

            <div class="border-t border-gray-100 pt-5">
                <h2 class="text-base font-bold text-gray-900">Ganti Password</h2>
                <p class="mt-1 text-sm text-gray-500">Biarkan kosong jika tidak ingin mengganti password.</p>
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700">Password Baru</label>
                <input id="password" type="password" name="password" minlength="8" autocomplete="new-password"
                       class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-brand-100">
                @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700">Konfirmasi Password Baru</label>
                <input id="password_confirmation" type="password" name="password_confirmation" minlength="8" autocomplete="new-password"
                       class="mt-2 w-full rounded-xl border border-gray-200 px-4 py-3 text-sm outline-none transition focus:border-brand-600 focus:ring-4 focus:ring-brand-100">
            </div>

            <button type="submit"
                    class="w-full rounded-xl bg-brand-600 px-5 py-3.5 font-semibold text-white shadow-sm transition hover:bg-brand-700 focus:outline-none focus:ring-4 focus:ring-brand-100 sm:w-auto">
                Simpan Perubahan
            </button>
        </form>
    </div>
</section>
@endsection
