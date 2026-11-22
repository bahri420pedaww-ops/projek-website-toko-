<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

/**
 * Mengubah isi keranjang menjadi pesanan. Seluruh proses berjalan dalam SATU transaksi
 * database: kalau satu langkah gagal, semuanya dibatalkan (stok tidak berkurang,
 * keranjang tidak kosong, pesanan tidak terbentuk).
 */
class CheckoutService
{
    public function __construct(private ShippingCalculator $shippingCalculator)
    {
    }

    /**
     * @throws DomainException jika keranjang kosong, produk tidak tersedia, stok kurang,
     *                         atau layanan pengiriman tidak melayani tujuan
     */
    public function placeOrder(User $user, Address $address, ShippingMethod $shipping, ?string $note = null): Order
    {
        if (! $shipping->is_active) {
            throw new DomainException('Layanan pengiriman yang dipilih tidak tersedia.');
        }

        return DB::transaction(function () use ($user, $address, $shipping, $note) {

            // 1. Kunci baris keranjang. Kalau tombol "Buat Pesanan" diklik dua kali,
            //    permintaan kedua menunggu di sini lalu mendapati keranjang sudah kosong.
            $cartItems = $user->cartItems()->orderBy('id')->lockForUpdate()->get();

            if ($cartItems->isEmpty()) {
                throw new DomainException('Keranjang masih kosong.');
            }

            // 2. Kunci baris produk (urut id agar tidak saling menunggu / deadlock)
            //    sehingga harga, berat, dan stok yang dibaca tidak berubah di tengah proses.
            $products = Product::query()
                ->whereIn('id', $cartItems->pluck('product_id'))
                ->orderBy('id')
                ->lockForUpdate()
                ->with('category')
                ->get()
                ->keyBy('id');

            // 3. Validasi tiap baris memakai data TERBARU dari database
            $lines = [];
            $subtotal = 0;
            $totalWeight = 0;

            foreach ($cartItems as $item) {
                $product = $products->get($item->product_id); // null jika produk sudah dihapus

                if (! $product || ! $product->is_active || ! $product->category?->is_active) {
                    throw new DomainException('Ada produk di keranjang yang tidak lagi tersedia. Periksa keranjangmu.');
                }

                if ($product->stock < $item->quantity) {
                    throw new DomainException(
                        "Stok \"{$product->name}\" tidak mencukupi (tersisa {$product->stock})."
                    );
                }

                $lines[] = [$product, $item->quantity];
                $subtotal += $product->price * $item->quantity;
                $totalWeight += $product->weight_gram * $item->quantity;
            }

            // 4. Ongkir dihitung server dari BERAT dan TUJUAN (angka dari browser diabaikan)
            $quote = $this->shippingCalculator->quote($shipping, $address->city, $address->province, $totalWeight);

            if ($quote === null) {
                throw new DomainException(
                    "{$shipping->courier} {$shipping->service} belum melayani tujuan alamat yang dipilih. "
                    . 'Pilih layanan lain.'
                );
            }

            $shippingCost = $quote['cost'];

            // 5. Buat pesanan. Alamat, harga, berat, dan ongkir DISALIN (snapshot) supaya riwayat
            //    pesanan tidak berubah kalau pembeli mengubah alamat atau admin mengubah harga.
            $order = Order::create([
                'order_code'         => Order::generateCode(),
                'user_id'            => $user->id,
                'shipping_method_id' => $shipping->id,
                'receiver_name'      => $address->receiver_name,
                'receiver_phone'     => $address->receiver_phone,
                'shipping_address'   => collect([$address->street_address, $address->district])
                                            ->filter()->implode(', '),
                'city_name'          => $address->city ?? '-',
                'province_name'      => $address->province ?? '-',
                'postal_code'        => $address->postal_code,
                'total_weight_gram'  => $totalWeight,
                'subtotal'           => $subtotal,
                'shipping_cost'      => $shippingCost,
                'shipping_etd'       => $quote['etd'],
                'discount_total'     => 0,
                'grand_total'        => $subtotal + $shippingCost,
                'status'             => Order::PENDING,
                'note'               => $note,
            ]);

            // 6. Isi pesanan + kurangi stok. UPDATE bersyarat (stok >= jumlah) sebagai
            //    pengaman terakhir agar stok tidak pernah negatif.
            foreach ($lines as [$product, $quantity]) {
                $affected = Product::whereKey($product->id)
                    ->where('stock', '>=', $quantity)
                    ->decrement('stock', $quantity);

                if ($affected === 0) {
                    throw new DomainException("Stok \"{$product->name}\" tidak mencukupi.");
                }

                $order->items()->create([
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'price'        => $product->price,
                    'quantity'     => $quantity,
                ]);
            }

            // 7. Catat riwayat status pertama
            $order->histories()->create([
                'status'     => Order::PENDING,
                'note'       => 'Pesanan dibuat, menunggu pembayaran.',
                'changed_by' => $user->id,
            ]);

            // 8. Kosongkan keranjang
            $user->cartItems()->delete();

            return $order;
        });
    }
}
