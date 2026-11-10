<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Province;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'DKI Jakarta'    => ['Jakarta Pusat', 'Jakarta Selatan', 'Jakarta Barat'],
            'Jawa Barat'     => ['Bandung', 'Bekasi', 'Depok', 'Bogor'],
            'Jawa Tengah'    => ['Semarang', 'Surakarta'],
            'Jawa Timur'     => ['Surabaya', 'Malang'],
            'Sumatera Utara' => ['Medan'],
            'Sumatera Barat' => ['Padang', 'Bukittinggi', 'Payakumbuh'],
            'Riau'           => ['Pekanbaru'],
            'Sumatera Selatan' => ['Palembang'],
            'Sulawesi Selatan' => ['Makassar'],
            'Bali'           => ['Denpasar'],
        ];

        foreach ($data as $provinceName => $cities) {
            $province = Province::firstOrCreate(['name' => $provinceName]);

            foreach ($cities as $cityName) {
                City::firstOrCreate([
                    'province_id' => $province->id,
                    'name'        => $cityName,
                ]);
            }
        }
    }
}
