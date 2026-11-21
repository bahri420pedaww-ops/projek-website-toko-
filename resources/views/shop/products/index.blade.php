{{-- PLACEHOLDER (backend). Variabel: $products (paginator), $categories (dengan children),
     $selectedCategory (boleh null), $keyword, $sort --}}
@extends('shop.layout')

@section('title', 'Produk')

@section('content')
    <div class="flex flex-col md:flex-row gap-8">

        {{-- Filter kategori --}}
        <aside class="md:w-56 shrink-0">
            <h2 class="font-semibold mb-3">Kategori</h2>
            <ul class="space-y-1 text-sm">
                <li>
                    <a href="{{ route('shop.products.index', request()->except(['page', 'kategori'])) }}"
                       class="{{ $selectedCategory ? 'text-gray-600' : 'font-semibold' }} hover:underline">
                        Semua kategori
                    </a>
                </li>
                @foreach ($categories as $category)
                    <li>
                        <a href="{{ route('shop.products.index', array_merge(request()->except(['page', 'kategori']), ['kategori' => $category->slug])) }}"
                           class="{{ $selectedCategory?->id === $category->id ? 'font-semibold' : 'text-gray-600' }} hover:underline">
                            {{ $category->name }}
                        </a>
                        @if ($category->children->isNotEmpty())
                            <ul class="ms-4 mt-1 space-y-1">
                                @foreach ($category->children as $child)
                                    <li>
                                        <a href="{{ route('shop.products.index', array_merge(request()->except(['page', 'kategori']), ['kategori' => $child->slug])) }}"
                                           class="{{ $selectedCategory?->id === $child->id ? 'font-semibold' : 'text-gray-500' }} hover:underline">
                                            {{ $child->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </aside>

        {{-- Daftar produk --}}
        <section class="flex-1">
            <form method="GET" action="{{ route('shop.products.index') }}" class="mb-6 flex flex-wrap gap-2">
                @if ($selectedCategory)
                    <input type="hidden" name="kategori" value="{{ $selectedCategory->slug }}">
                @endif
                <input type="text" name="q" value="{{ $keyword }}" placeholder="Cari produk..."
                       class="border-gray-300 rounded-md shadow-sm text-sm">
                <select name="sort" onchange="this.form.submit()"
                        class="border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="terbaru"  @selected($sort === 'terbaru')>Terbaru</option>
                    <option value="termurah" @selected($sort === 'termurah')>Harga termurah</option>
                    <option value="termahal" @selected($sort === 'termahal')>Harga termahal</option>
                </select>
                <button class="px-4 py-2 bg-gray-800 text-white text-xs font-semibold uppercase rounded-md">Cari</button>
            </form>

            @if ($products->isEmpty())
                <p class="text-gray-500">Produk tidak ditemukan.</p>
            @else
                <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach ($products as $product)
                        @include('shop.products._card', ['product' => $product])
                    @endforeach
                </div>

                <div class="mt-6">{{ $products->links() }}</div>
            @endif
        </section>
    </div>
@endsection
