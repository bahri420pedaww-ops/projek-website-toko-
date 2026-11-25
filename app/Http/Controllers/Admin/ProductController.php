<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class ProductController extends Controller
{
    public function index(): View
    {
        return view('admin.products.index', [
            'products' => Product::with(['category', 'primaryImage'])->latest()->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'categories' => Category::orderBy('name')->get(),
            'product' => new Product(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);
        $image = $validated['image'];
        unset($validated['image']);
        $validated['slug'] = $validated['slug'] ?? $this->uniqueSlug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');
        $imagePath = $image->store('products', 'public');

        if (!$imagePath) {
            throw new \RuntimeException('Gambar produk gagal disimpan.');
        }

        try {
            DB::transaction(function () use ($validated, $imagePath) {
                $product = Product::create($validated);
                $product->images()->create([
                    'path' => $imagePath,
                    'is_primary' => true,
                    'sort_order' => 0,
                ]);
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($imagePath);
            throw $exception;
        }

        return Redirect::route('admin.products.index')->with('success', 'Produk berhasil dibuat.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', [
            'categories' => Category::orderBy('name')->get(),
            'product' => $product->load('primaryImage'),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validatedData($request, $product);
        $image = $validated['image'] ?? null;
        unset($validated['image']);
        $validated['slug'] = $validated['slug'] ?? $product->slug;
        $validated['is_active'] = $request->boolean('is_active');
        $imagePath = $image ? $image->store('products', 'public') : null;

        if ($image && !$imagePath) {
            throw new \RuntimeException('Gambar produk gagal disimpan.');
        }

        $oldPrimaryImage = $imagePath ? $product->primaryImage()->first() : null;
        $oldImagePath = $oldPrimaryImage?->path;
        try {
            DB::transaction(function () use ($product, $validated, $imagePath, $oldPrimaryImage) {
                $product->update($validated);

                if ($imagePath) {
                    $product->images()->update(['is_primary' => false]);
                    if ($oldPrimaryImage) {
                        $oldPrimaryImage->update([
                            'path' => $imagePath,
                            'is_primary' => true,
                            'sort_order' => 0,
                        ]);
                    } else {
                        $product->images()->create([
                            'path' => $imagePath,
                            'is_primary' => true,
                            'sort_order' => 0,
                        ]);
                    }
                }
            });
        } catch (Throwable $exception) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            throw $exception;
        }

        if ($oldImagePath && $oldImagePath !== $imagePath && str_starts_with($oldImagePath, 'products/')) {
            $disk = Storage::disk('public');
            if (!$disk->delete($oldImagePath) && $disk->exists($oldImagePath)) {
                Log::warning('Gambar produk lama gagal dihapus.', ['path' => $oldImagePath]);
            }
        }

        return Redirect::route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return Redirect::route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }

    private function validatedData(Request $request, ?Product $product = null): array
    {
        $slugRule = Rule::unique('products', 'slug');
        if ($product) {
            $slugRule->ignore($product->id);
        }

        return $request->validate([
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')->whereNull('deleted_at')],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', $slugRule],
            'description' => ['nullable', 'string', 'max:10000'],
            'price' => ['required', 'integer', 'min:0', 'max:9223372036854775807'],
            'stock' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'weight_gram' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'is_active' => ['sometimes', 'boolean'],
            'image' => [$product ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
    }

    private function uniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $suffix = 2;

        while (Product::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $suffix++;
        }

        return $slug;
    }
}
