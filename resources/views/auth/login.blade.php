@extends('layouts.app')
@section('title', 'Masuk - StyleShop')
@section('content')
<div class="max-w-md mx-auto px-4 py-12">
    <h1 class="text-2xl font-bold mb-6">Masuk</h1>
    <form action="{{ route('login.store') }}" method="POST" class="space-y-4 rounded-xl bg-white p-6 shadow-sm">
        @csrf
        @if ($errors->any())
            <div class="rounded-lg bg-red-50 p-3 text-red-700">{{ $errors->first() }}</div>
        @endif
        <label class="block">Email
            <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="mt-1 w-full rounded-lg border p-3">
        </label>
        <label class="block">Kata sandi
            <input type="password" name="password" required autocomplete="current-password" class="mt-1 w-full rounded-lg border p-3">
        </label>
        <label class="flex items-center gap-2">
            <input type="checkbox" name="remember" value="1">
            Ingat saya
        </label>
        <button type="submit" class="w-full rounded-lg bg-brand-600 py-3 font-semibold text-white">Masuk</button>
    </form>
</div>
@endsection
