<?php

namespace Database\Seeders;

use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

/**
 * Tarif per kg CONTOH untuk tiap layanan, per zona (1 = satu kota ... 4 = beda pulau).
 * Sesuaikan dengan tarif kurir yang sebenarnya lewat halaman admin (/admin/shipping-methods).
 * Aman dijalankan berulang: tarif yang sudah ada tidak ditimpa.
 */
class ShippingRateSeeder extends Seeder
{
    public function run(): void
    {
        $definitions = [
            ['JNE', 'REG', [8000, 10000, 15000, 25000], ['1-2 hari', '2-3 hari', '3-5 hari', '5-9 hari']],
            ['JNE', 'YES', [15000, 20000, 30000, 45000], ['1 hari', '1-2 hari', '2-3 hari', '3-5 hari']],
            ['J&T', 'Express', [9000, 11000, 16000, 27000], ['1-2 hari', '2-3 hari', '3-5 hari', '5-9 hari']],
            ['SiCepat', 'REG', [8000, 10000, 14000, 24000], ['1-2 hari', '2-3 hari', '3-5 hari', '5-8 hari']],
        ];

        foreach ($definitions as [$courier, $service, $prices, $etds]) {
            $method = ShippingMethod::firstOrCreate(
                ['courier' => $courier, 'service' => $service],
                ['cost' => 0, 'is_active' => true]
            );

            foreach ($prices as $index => $price) {
                $method->rates()->firstOrCreate(
                    ['zone' => $index + 1],
                    ['price_per_kg' => $price, 'estimated_days' => $etds[$index]]
                );
            }
        }
    }
}
