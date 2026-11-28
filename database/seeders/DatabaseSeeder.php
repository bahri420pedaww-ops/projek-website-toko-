<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

        $categoryIds = [];
        foreach ([
            ['slug' => 'kaos', 'name' => 'Kaos'],
            ['slug' => 'kemeja', 'name' => 'Kemeja'],
            ['slug' => 'celana', 'name' => 'Celana'],
        ] as $category) {
            $categoryIds[$category['slug']] = Category::updateOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name'], 'is_active' => true]
            )->id;
        }

        Product::whereIn('slug', [
            'produk-contoh-1',
            'produk-contoh-2',
            'produk-contoh-3',
            'kaos-basic-premium',
            'kemeja-flanel-kasual',
            'celana-kasual-modern',
        ])->delete();

        $products = [
            [
                'name' => 'Kaos Polos Premium',
                'category' => 'kaos',
                'description' => 'Kaos polos berbahan katun lembut dengan potongan nyaman untuk aktivitas harian.',
                'weight_gram' => 250,
                'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=500&q=80',
            ],
            [
                'name' => 'Kaos Basic Cotton',
                'category' => 'kaos',
                'description' => 'Kaos basic dengan bahan cotton breathable yang ringan dan mudah dipadukan.',
                'weight_gram' => 240,
                'image' => 'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=500&q=80',
            ],
            [
                'name' => 'Kaos Oversize Essential',
                'category' => 'kaos',
                'description' => 'Kaos oversize dengan siluet santai dan bahan nyaman untuk gaya kasual.',
                'weight_gram' => 280,
                'image' => 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=500&q=80',
            ],
            [
                'name' => 'Kemeja Flanel Kotak',
                'category' => 'kemeja',
                'description' => 'Kemeja flanel bermotif kotak dengan tekstur lembut untuk tampilan kasual.',
                'weight_gram' => 380,
                'image' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=500&q=80',
            ],
            [
                'name' => 'Kemeja Oxford Klasik',
                'category' => 'kemeja',
                'description' => 'Kemeja Oxford klasik dengan kerah rapi, cocok untuk kerja maupun acara santai.',
                'weight_gram' => 360,
                'image' => 'https://images.unsplash.com/photo-1589310243389-96a5483213a8?w=500&q=80',
            ],
            [
                'name' => 'Kemeja Lengan Panjang Casual',
                'category' => 'kemeja',
                'description' => 'Kemeja lengan panjang berpotongan modern untuk gaya rapi yang tetap nyaman.',
                'weight_gram' => 370,
                'image' => 'https://images.unsplash.com/photo-1598033129183-c4f50c736f10?w=500&q=80',
            ],
            [
                'name' => 'Kemeja Denim Urban',
                'category' => 'kemeja',
                'description' => 'Kemeja denim ringan dengan nuansa urban, mudah dipadukan dengan berbagai outfit.',
                'weight_gram' => 420,
                'image' => 'https://images.unsplash.com/photo-1620012253295-c15cc3e65df4?w=500&q=80',
            ],
            [
                'name' => 'Celana Chino Slim Fit',
                'category' => 'celana',
                'description' => 'Celana chino slim fit dengan bahan fleksibel untuk tampilan smart-casual.',
                'weight_gram' => 520,
                'image' => 'https://images.unsplash.com/photo-1542272604-787c3835535d?w=500&q=80',
            ],
            [
                'name' => 'Celana Jeans Straight Fit',
                'category' => 'celana',
                'description' => 'Celana jeans straight fit dengan denim kuat dan potongan klasik serbaguna.',
                'weight_gram' => 650,
                'image' => 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?w=500&q=80',
            ],
            [
                'name' => 'Celana Cargo Utility',
                'category' => 'celana',
                'description' => 'Celana cargo dengan kantong fungsional dan bahan tangguh untuk kegiatan sehari-hari.',
                'weight_gram' => 600,
                'image' => 'https://images.unsplash.com/photo-1584865288642-42078ebc45cc?w=500&q=80',
            ],
            [
                'name' => 'Kaos Graphic Minimalis',
                'category' => 'kaos',
                'description' => 'Kaos grafis dengan desain minimalis dan bahan lembut untuk gaya akhir pekan.',
                'weight_gram' => 260,
                'image' => 'https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=500&q=80',
            ],
            [
                'name' => 'Kemeja Linen Santai',
                'category' => 'kemeja',
                'description' => 'Kemeja linen ringan dengan sirkulasi udara baik untuk suasana santai dan hangat.',
                'weight_gram' => 300,
                'image' => 'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=500&q=80',
            ],
            [
                'name' => 'Celana Bahan Formal',
                'category' => 'celana',
                'description' => 'Celana bahan dengan potongan rapi, nyaman dikenakan untuk bekerja dan acara formal.',
                'weight_gram' => 480,
                'image' => 'https://images.unsplash.com/photo-1604176354204-9268737828e4?w=500&q=80',
            ],
            [
                'name' => 'Kaos Combed 30s',
                'category' => 'kaos',
                'description' => 'Kaos combed 30s yang halus, ringan, dan nyaman untuk pemakaian sepanjang hari.',
                'weight_gram' => 230,
                'image' => 'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?w=500&q=80',
            ],
            [
                'name' => 'Kemeja Motif Garis',
                'category' => 'kemeja',
                'description' => 'Kemeja bermotif garis sederhana dengan detail rapi untuk gaya semi-formal.',
                'weight_gram' => 350,
                'image' => 'https://images.unsplash.com/photo-1593032465175-481ac7f401a0?w=500&q=80',
            ],
            [
                'name' => 'Celana Jogger Casual',
                'category' => 'celana',
                'description' => 'Celana jogger dengan pinggang elastis dan potongan santai untuk mobilitas tinggi.',
                'weight_gram' => 430,
                'image' => 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=500&q=80',
            ],
            [
                'name' => 'Kaos Raglan Sporty',
                'category' => 'kaos',
                'description' => 'Kaos raglan bergaya sporty dengan kombinasi warna yang mudah dipadukan.',
                'weight_gram' => 270,
                'image' => 'https://images.unsplash.com/photo-1562157873-818bc0726f68?w=500&q=80',
            ],
            [
                'name' => 'Kemeja Kerja Slim Fit',
                'category' => 'kemeja',
                'description' => 'Kemeja kerja slim fit dengan tampilan profesional dan bahan yang tetap nyaman.',
                'weight_gram' => 340,
                'image' => 'https://images.unsplash.com/photo-1516257984-b1b4d8c9230b?w=500&q=80',
            ],
            [
                'name' => 'Celana Denim Tapered',
                'category' => 'celana',
                'description' => 'Celana denim tapered dengan potongan mengecil di bagian bawah untuk tampilan modern.',
                'weight_gram' => 620,
                'image' => 'https://images.unsplash.com/photo-1475178626620-a4d074967452?w=500&q=80',
            ],
            [
                'name' => 'Kaos Pocket Everyday',
                'category' => 'kaos',
                'description' => 'Kaos kasual dengan detail saku sederhana dan bahan katun yang nyaman.',
                'weight_gram' => 250,
                'image' => 'https://images.unsplash.com/photo-1554568218-0f1715e72254?w=500&q=80',
            ],
        ];

        foreach ($products as $seedProduct) {
            $slug = Str::slug($seedProduct['name']);
            $product = Product::withTrashed()->updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $categoryIds[$seedProduct['category']],
                    'slug' => $slug,
                    'name' => $seedProduct['name'],
                    'description' => $seedProduct['description'],
                    'price' => random_int(100000, 300000),
                    'stock' => random_int(10, 50),
                    'weight_gram' => $seedProduct['weight_gram'],
                    'is_active' => true,
                ]
            );
            if ($product->trashed()) {
                $product->restore();
            }

            $product->images()->updateOrCreate(
                ['is_primary' => true],
                ['path' => $seedProduct['image'], 'sort_order' => 0]
            );
        }
    }
}
