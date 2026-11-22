<?php

namespace App\Services;

use App\Models\ShippingMethod;

/**
 * Menghitung ongkir dari BERAT dan KOTA/PROVINSI TUJUAN.
 *
 *   zona   = ditentukan dari lokasi toko (config/shipping.php) ke tujuan
 *   ongkir = tarif per kg (zona itu) x berat yang ditagih
 *   berat ditagih = berat total dalam kg, dibulatkan ke atas, minimal 1 kg
 *
 * Alamat berupa teks bebas, jadi nama dinormalkan dulu (huruf kecil, tanpa tanda baca,
 * awalan "Kota"/"Kabupaten"/"Kab." dibuang) sebelum dibandingkan.
 */
class ShippingCalculator
{
    public const ZONE_SAME_CITY     = 1;
    public const ZONE_SAME_PROVINCE = 2;
    public const ZONE_SAME_ISLAND   = 3;
    public const ZONE_OTHER_ISLAND  = 4;

    public const ZONE_LABELS = [
        1 => 'Zona 1: satu kota dengan toko',
        2 => 'Zona 2: satu provinsi dengan toko',
        3 => 'Zona 3: satu pulau besar dengan toko',
        4 => 'Zona 4: beda pulau',
    ];

    /** Zona tujuan (1 sampai 4). Nama yang tidak dikenali dianggap zona 4. */
    public function zoneFor(?string $city, ?string $province): int
    {
        $originCity = $this->normalizeCity((string) config('shipping.origin_city'));
        $originProvince = $this->normalizeProvince((string) config('shipping.origin_province'));

        $destCity = $this->normalizeCity((string) $city);
        $destProvince = $this->normalizeProvince((string) $province);

        if ($destProvince !== '' && $destProvince === $originProvince) {
            return ($destCity !== '' && $destCity === $originCity)
                ? self::ZONE_SAME_CITY
                : self::ZONE_SAME_PROVINCE;
        }

        $destIsland = $this->islandOf($destProvince);
        $originIsland = $this->islandOf($originProvince);

        if ($destIsland !== null && $destIsland === $originIsland) {
            return self::ZONE_SAME_ISLAND;
        }

        return self::ZONE_OTHER_ISLAND;
    }

    /** Berat yang ditagih dalam kg: dibulatkan ke atas, minimal 1. */
    public function billableKg(int $weightGram): int
    {
        return max(1, (int) ceil($weightGram / 1000));
    }

    /**
     * Hitung ongkir satu layanan. Mengembalikan null kalau layanan itu
     * belum punya tarif untuk zona tujuan (artinya tidak melayani tujuan tersebut).
     *
     * @return array{zone:int, billable_kg:int, cost:int, etd:?string}|null
     */
    public function quote(ShippingMethod $method, ?string $city, ?string $province, int $weightGram): ?array
    {
        $zone = $this->zoneFor($city, $province);
        $rate = $method->rates->firstWhere('zone', $zone);

        if (! $rate) {
            return null;
        }

        $kg = $this->billableKg($weightGram);

        return [
            'zone'        => $zone,
            'billable_kg' => $kg,
            'cost'        => $rate->price_per_kg * $kg,
            'etd'         => $rate->estimated_days,
        ];
    }

    public function normalize(?string $value): string
    {
        $value = mb_strtolower(trim((string) $value));
        $value = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $value);   // tanda baca menjadi spasi
        $value = preg_replace('/\s+/u', ' ', $value);

        return trim($value);
    }

    private function normalizeCity(string $city): string
    {
        $n = $this->normalize($city);

        return trim(preg_replace('/^(kota administrasi|kota adm|kota|kabupaten|kab)\s+/u', '', $n));
    }

    private function normalizeProvince(string $province): string
    {
        $n = $this->normalize($province);

        return trim(preg_replace('/^(provinsi|prov)\s+/u', '', $n));
    }

    private function islandOf(string $normalizedProvince): ?string
    {
        if ($normalizedProvince === '') {
            return null;
        }

        foreach (config('shipping.islands', []) as $island => $provinces) {
            if (in_array($normalizedProvince, $provinces, true)) {
                return $island;
            }
        }

        return null;
    }
}
