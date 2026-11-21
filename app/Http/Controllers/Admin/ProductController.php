<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\GeneratesUniqueSlug;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    use GeneratesUniqueSlug;

    public function index(Request $request)
    {
        $products = Product::with(['category', 'primaryImage'])
            ->when($request->q, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->category_id, fn ($q, $c) => $q->where('category_id', $c))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(ProductRequest $request)
    {
        $data = $request->safe()->except('images');
        $data['slug'] = $this->uniqueSlug(Product::class, $data['name']);
        $data['is_active'] = $request->boolean('is_active');

        DB::transaction(function () use ($data, $request) {
            $product = Product::create($data);
            $this->storeImages($product, $request->file('images', []));
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $product->load('images');
        $categories = Category::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $data = $request->safe()->except('images');

        if ($data['name'] !== $product->name) {
            $data['slug'] = $this->uniqueSlug(Product::class, $data['name'], $product->id);
        }
        $data['is_active'] = $request->boolean('is_active');

        DB::transaction(function () use ($data, $request, $product) {
            $product->update($data);
            $this->storeImages($product, $request->file('images', []));
        });

        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        // Soft delete: riwayat pesanan yang memuat produk ini tetap utuh
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * Simpan gambar baru. Gambar pertama otomatis menjadi gambar utama
     * kalau produk belum punya gambar utama.
     */
    private function storeImages(Product $product, array $files): void
    {
        $hasPrimary = ProductImage::where('product_id', $product->id)
            ->where('is_primary', true)
            ->exists();

        $order = (int) ProductImage::where('product_id', $product->id)->max('sort_order');

        foreach ($files as $file) {
            $order++;

            ProductImage::create([
                'product_id' => $product->id,
                'path'       => $file->store('products', 'public'),
                'is_primary' => ! $hasPrimary,
                'sort_order' => $order,
            ]);

            $hasPrimary = true;
        }
    }
}
