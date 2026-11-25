<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function home(): View
    {
        return view('home', [
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(),
            'products' => Product::active()->with('primaryImage')->latest()->take(8)->get(),
        ]);
    }

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'category' => ['sometimes', 'string', 'max:255', 'alpha_dash'],
            'sort' => ['sometimes', 'string', Rule::in(['newest', 'price_asc', 'price_desc'])],
        ]);
        $query = Product::active()->with(['category', 'primaryImage']);

        if (!empty($filters['category'])) {
            $query->whereHas('category', function ($categoryQuery) use ($filters) {
                $categoryQuery->where('slug', $filters['category'])
                    ->where('is_active', true);
            });
        }

        $sorts = [
            'newest' => ['created_at', 'desc'],
            'price_asc' => ['price', 'asc'],
            'price_desc' => ['price', 'desc'],
        ];
        $sort = $filters['sort'] ?? 'newest';
        [$column, $direction] = $sorts[$sort];

        return view('products.index', [
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(),
            'products' => $query->orderBy($column, $direction)->paginate(12)->withQueryString(),
            'activeCategory' => $filters['category'] ?? null,
            'activeSort' => $sort,
        ]);
    }

    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        return view('products.show', [
            'product' => $product->load(['category', 'images']),
        ]);
    }
}
