<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin StyleShop')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800 antialiased">
    <div class="min-h-screen lg:flex">
        <aside class="bg-slate-900 text-slate-100 lg:fixed lg:inset-y-0 lg:left-0 lg:flex lg:w-72 lg:flex-col">
            <div class="border-b border-slate-800 px-6 py-6">
                <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold tracking-tight">
                    Style<span class="text-indigo-400">Shop</span>
                    <span class="mt-1 block text-xs font-medium uppercase tracking-[0.2em] text-slate-400">Admin Panel</span>
                </a>
            </div>

            <nav class="flex gap-2 overflow-x-auto p-4 lg:flex-1 lg:flex-col lg:overflow-visible">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex shrink-0 items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-950/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <span aria-hidden="true">▦</span> Dashboard
                </a>
                <a href="{{ route('admin.products.index') }}"
                   class="flex shrink-0 items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('admin.products.*') ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-950/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <span aria-hidden="true">▣</span> Produk
                </a>
                <a href="{{ route('admin.orders.index') }}"
                   class="flex shrink-0 items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition {{ request()->routeIs('admin.orders.*') ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-950/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <span aria-hidden="true">▤</span> Pesanan
                </a>
            </nav>

            <div class="hidden border-t border-slate-800 p-4 lg:block">
                <div class="mb-4 truncate px-2">
                    <p class="truncate text-sm font-semibold">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-slate-400">{{ auth()->user()->email }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full rounded-xl border border-slate-700 px-4 py-3 text-left text-sm text-slate-300 transition hover:border-rose-400/40 hover:bg-rose-500/10 hover:text-rose-200">
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <div class="min-w-0 flex-1 lg:ml-72">
            <header class="flex items-center justify-between border-b border-slate-200 bg-white px-5 py-4 sm:px-8">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-600">StyleShop</p>
                    <h1 class="mt-1 text-lg font-bold text-slate-900">@yield('page-heading', 'Panel Admin')</h1>
                </div>
                <div class="flex items-center gap-3">
                    <span class="hidden text-sm text-slate-500 sm:inline">{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="lg:hidden">
                        @csrf
                        <button type="submit" class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-medium text-white">Keluar</button>
                    </form>
                </div>
            </header>

            <main class="px-4 py-6 sm:px-8 sm:py-8">
                @if (session('success'))
                    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" role="status">
                        {{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
                        <p class="font-semibold">Periksa kembali data yang dimasukkan:</p>
                        <ul class="mt-2 list-inside list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
