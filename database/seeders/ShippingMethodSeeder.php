<?php

namespace Database\Seeders;

use App\Models\ShippingMethod;
use Illuminate\Database\Seeder;

class ShippingMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['courier' => 'JNE',    'service' => 'REG',     'cost' => 15000],
            ['courier' => 'JNE',    'service' => 'YES',     'cost' => 30000],
            ['courier' => 'J&T',    'service' => 'Express', 'cost' => 14000],
            ['courier' => 'SiCepat', 'service' => 'REG',    'cost' => 13000],
        ];

        foreach ($methods as $method) {
            ShippingMethod::firstOrCreate(
                ['courier' => $method['courier'], 'service' => $method['service']],
                ['cost' => $method['cost'], 'is_active' => true]
            );
        }
    }
}
