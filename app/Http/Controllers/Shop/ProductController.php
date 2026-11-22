<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Katalog produk (publik).
     * Parameter URL: q (cari nama), kategori (slug), sort (terbaru|termurah|termahal), page
     */
    public function index(Request $request)
    {
        $sort = in_array($request->input('sort'), ['terbaru', 'termurah', 'termahal'], true)
            ? $request->input('sort')
            : 'terbaru';

        $keyword = trim((string) $request->input('q'));

        // Filter kategori: kategori yang dipilih + sub-kategorinya
        $selectedCategory = null;
        $categoryIds = null;

        if ($slug = $request->input('kategori')) {
            $selectedCategory = Category::where('slug', $slug)
                ->where('is_active', true)
                ->first();

            if ($selectedCategory) {
                $categoryIds = $selectedCategory->children()
                    ->where('is_active', true)
                    ->pluck('id')
                    ->push($selectedCategory->id);
            }
        }

        $query = Product::query()
            ->active()
            ->whereHas('category', fn ($q) => $q->where('is_active', true))
            ->with(['category', 'primaryImage'])
            ->when($keyword !== '', fn ($q) => $q->where('name', 'like', "%{$keyword}%"))
            ->when($categoryIds, fn ($q, $ids) => $q->whereIn('category_id', $ids));

        match ($sort) {
            'termurah' => $query->orderBy('price'),
            'termahal' => $query->orderByDesc('price'),
            default    => $query->latest(),
        };
        $query->orderByDesc('id'); // urutan stabil untuk pagination

        $products = $query->paginate(12)->withQueryString();

        // Daftar kategori utama beserta sub-kategorinya (untuk menu filter)
        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->where('is_active', true)->orderBy('name')])
            ->orderBy('name')
            ->get();

        return view('shop.products.index', compact(
            'products', 'categories', 'selectedCategory', 'keyword', 'sort'
        ));
    }

    /**
     * Detail produk (publik). Produk nonaktif / terhapus / kategori nonaktif => 404.
     */
    public function show(string $slug)
    {
        $product = Product::query()
            ->active()
            ->whereHas('category', fn ($q) => $q->where('is_active', true))
            ->where('slug', $slug)
            ->with(['category', 'images'])
            ->firstOrFail();

        $related = Product::query()
            ->active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with(['category', 'primaryImage'])
            ->latest()
            ->take(4)
            ->get();

        return view('shop.products.show', compact('product', 'related'));
    }
}
