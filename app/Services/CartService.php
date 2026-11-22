<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use DomainException;

/**
 * Seluruh logika keranjang ada di sini, supaya controller tetap ringan
 * dan langkah Checkout nanti bisa memakai ulang summary() untuk validasi.
 */
class CartService
{
    public const MAX_QTY = 99;

    /**
     * Ringkasan keranjang: baris, subtotal, dan apakah ada masalah (stok/produk).
     * Baris bermasalah TIDAK dihitung ke subtotal.
     */
    public function summary(User $user): array
    {
        $items = $user->cartItems()
            ->with(['product.primaryImage', 'product.category'])
            ->latest()
            ->get();

        $subtotal = 0;
        $totalQty = 0;
        $hasProblem = false;

        $lines = $items->map(function (CartItem $item) use (&$subtotal, &$totalQty, &$hasProblem) {
            $product = $item->product; // null kalau produk sudah dihapus
            $problem = null;

            if (! $product || ! $this->isPurchasable($product)) {
                $problem = 'Produk ini tidak lagi tersedia. Silakan hapus dari keranjang.';
            } elseif ($product->stock < 1) {
                $problem = 'Stok habis.';
            } elseif ($item->quantity > $product->stock) {
                $problem = "Stok tersisa {$product->stock}. Kurangi jumlah pesanan.";
            }

            $lineTotal = 0;
            if ($problem === null) {
                $lineTotal = $product->price * $item->quantity;
                $subtotal += $lineTotal;
                $totalQty += $item->quantity;
            } else {
                $hasProblem = true;
            }

            return (object) [
                'id'           => $item->id,
                'product'      => $product,
                'quantity'     => $item->quantity,
                'unit_price'   => $product?->price ?? 0,
                'line_total'   => $lineTotal,
                'max_quantity' => $product ? min($product->stock, self::MAX_QTY) : 0,
                'problem'      => $problem,
            ];
        });

        return [
            'items'          => $lines,
            'subtotal'       => $subtotal,
            'total_quantity' => $totalQty,
            'has_problem'    => $hasProblem,
            'is_empty'       => $lines->isEmpty(),
        ];
    }

    /**
     * Tambah produk ke keranjang. Kalau sudah ada, jumlahnya ditambah.
     * Mengembalikan pesan peringatan bila jumlah disesuaikan dengan stok, atau null.
     *
     * @throws DomainException
     */
    public function add(User $user, Product $product, int $quantity): ?string
    {
        if (! $this->isPurchasable($product)) {
            throw new DomainException('Produk ini tidak tersedia.');
        }

        if ($product->stock < 1) {
            throw new DomainException('Stok produk habis.');
        }

        $item = $user->cartItems()->firstOrNew(['product_id' => $product->id]);

        $newQty = ($item->exists ? $item->quantity : 0) + $quantity;
        [$newQty, $notice] = $this->limitToStock($newQty, $product);

        $item->quantity = $newQty;
        $item->save();

        return $notice;
    }

    /**
     * Ubah jumlah satu item. Hanya item milik user yang bisa diubah (selain itu 404).
     *
     * @throws DomainException
     */
    public function update(User $user, int $itemId, int $quantity): ?string
    {
        $item = $user->cartItems()->with('product.category')->findOrFail($itemId);
        $product = $item->product;

        if (! $product || ! $this->isPurchasable($product) || $product->stock < 1) {
            throw new DomainException('Produk ini tidak lagi tersedia. Silakan hapus dari keranjang.');
        }

        [$quantity, $notice] = $this->limitToStock($quantity, $product);

        $item->update(['quantity' => $quantity]);

        return $notice;
    }

    public function remove(User $user, int $itemId): void
    {
        $user->cartItems()->whereKey($itemId)->delete();
    }

    public function clear(User $user): void
    {
        $user->cartItems()->delete();
    }

    private function isPurchasable(Product $product): bool
    {
        $product->loadMissing('category');

        return $product->is_active && (bool) $product->category?->is_active;
    }

    /** @return array{0:int, 1:?string} */
    private function limitToStock(int $quantity, Product $product): array
    {
        $limit = min($product->stock, self::MAX_QTY);

        if ($quantity > $limit) {
            return [$limit, "Jumlah disesuaikan dengan stok yang tersedia ({$limit})."];
        }

        return [$quantity, null];
    }
}
