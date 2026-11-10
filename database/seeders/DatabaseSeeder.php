<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LocationSeeder::class,
            ShippingMethodSeeder::class,
        ]);

        User::firstOrCreate(['email' => 'admin@toko.test'], [
            'name'     => 'Admin Toko',
            'phone'    => '081200000001',
            'role'     => 'admin',
            'password' => Hash::make('password'),
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
