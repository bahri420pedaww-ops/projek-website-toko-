<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LocationSeeder::class,
            ShippingMethodSeeder::class,
            AdminUserSeeder::class,
        ]);

        User::firstOrCreate(['email' => 'pembeli@toko.test'], [
            'name'     => 'Pembeli Contoh',
            'phone'    => '081200000002',
            'role'     => 'customer',
            'password' => Hash::make('password'),
        ]);

        $kategori = Category::firstOrCreate(
            ['slug' => 'umum'],
            ['name' => 'Umum', 'is_active' => true]
        );

        foreach ([1, 2, 3] as $i) {
            Product::firstOrCreate(['slug' => "produk-contoh-$i"], [
                'category_id' => $kategori->id,
                'name'        => "Produk Contoh $i",
                'description' => "Deskripsi produk contoh $i.",
                'price'       => 25000 * $i,
                'stock'       => 10,
                'weight_gram' => 500,
                'is_active'   => true,
            ]);
        }
    }
}
