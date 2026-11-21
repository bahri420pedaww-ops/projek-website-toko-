<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    public function destroy(Product $product, ProductImage $image)
    {
        abort_unless($image->product_id === $product->id, 404);

        DB::transaction(function () use ($product, $image) {
            $wasPrimary = $image->is_primary;

            Storage::disk('public')->delete($image->path);
            $image->delete();

            // Kalau yang dihapus gambar utama, jadikan gambar berikutnya sebagai utama
            if ($wasPrimary) {
                ProductImage::where('product_id', $product->id)
                    ->orderBy('sort_order')
                    ->first()
                    ?->update(['is_primary' => true]);
            }
        });

        return back()->with('success', 'Gambar berhasil dihapus.');
    }

    public function setPrimary(Product $product, ProductImage $image)
    {
        abort_unless($image->product_id === $product->id, 404);

        DB::transaction(function () use ($product, $image) {
            ProductImage::where('product_id', $product->id)->update(['is_primary' => false]);
            $image->update(['is_primary' => true]);
        });

        return back()->with('success', 'Gambar utama diperbarui.');
    }
}
