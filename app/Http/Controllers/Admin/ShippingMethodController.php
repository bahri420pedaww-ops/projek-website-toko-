<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ShippingMethodRequest;
use App\Models\ShippingMethod;
use Illuminate\Support\Facades\DB;

/**
 * Admin mengelola layanan kurir dan tarif per kg untuk tiap zona.
 * Layanan tidak dihapus (pesanan lama merujuknya); cukup dinonaktifkan.
 */
class ShippingMethodController extends Controller
{
    public function index()
    {
        $methods = ShippingMethod::with('rates')
            ->orderBy('courier')
            ->orderBy('service')
            ->get();

        return view('admin.shipping.index', compact('methods'));
    }

    public function create()
    {
        return view('admin.shipping.create');
    }

    public function store(ShippingMethodRequest $request)
    {
        DB::transaction(function () use ($request) {
            $method = ShippingMethod::create([
                'courier'   => $request->input('courier'),
                'service'   => $request->input('service'),
                'cost'      => 0, // kolom lama, tidak dipakai lagi
                'is_active' => $request->boolean('is_active'),
            ]);

            $this->syncRates($method, $request->input('rates', []));
        });

        return redirect()->route('admin.shipping-methods.index')
            ->with('success', 'Layanan pengiriman berhasil ditambahkan.');
    }

    public function edit(ShippingMethod $method)
    {
        $method->load('rates');

        return view('admin.shipping.edit', compact('method'));
    }

    public function update(ShippingMethodRequest $request, ShippingMethod $method)
    {
        DB::transaction(function () use ($request, $method) {
            $method->update([
                'courier'   => $request->input('courier'),
                'service'   => $request->input('service'),
                'is_active' => $request->boolean('is_active'),
            ]);

            $this->syncRates($method, $request->input('rates', []));
        });

        return redirect()->route('admin.shipping-methods.index')
            ->with('success', 'Layanan pengiriman berhasil diperbarui.');
    }

    /** Simpan tarif zona 1 sampai 4. Tarif yang dikosongkan dihapus (zona tidak dilayani). */
    private function syncRates(ShippingMethod $method, array $rates): void
    {
        foreach (range(1, 4) as $zone) {
            $price = $rates[$zone]['price_per_kg'] ?? null;

            if ($price === null || $price === '') {
                $method->rates()->where('zone', $zone)->delete();
                continue;
            }

            $method->rates()->updateOrCreate(
                ['zone' => $zone],
                [
                    'price_per_kg'   => (int) $price,
                    'estimated_days' => $rates[$zone]['estimated_days'] ?? null,
                ]
            );
        }
    }
}
