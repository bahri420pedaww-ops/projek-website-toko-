{{-- PLACEHOLDER (backend). Opsional: $method (dengan rates) --}}
@php
    $method = $method ?? null;
    $labels = \App\Services\ShippingCalculator::ZONE_LABELS;
    $existing = $method ? $method->rates->keyBy('zone') : collect();
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <x-input-label for="courier" value="Kurir" />
        <x-text-input id="courier" name="courier" type="text" class="mt-1 block w-full"
                      :value="old('courier', $method?->courier)" placeholder="JNE" required />
        <x-input-error :messages="$errors->get('courier')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="service" value="Layanan" />
        <x-text-input id="service" name="service" type="text" class="mt-1 block w-full"
                      :value="old('service', $method?->service)" placeholder="REG" required />
        <x-input-error :messages="$errors->get('service')" class="mt-2" />
    </div>
</div>

<div>
    <h3 class="text-sm font-semibold text-gray-700">Tarif per kg menurut zona tujuan</h3>
    <p class="mt-1 text-xs text-gray-500">
        Ongkir = tarif per kg x berat yang ditagih (dibulatkan ke atas, minimal 1 kg).
        Kosongkan tarif zona kalau layanan ini tidak melayani zona tersebut.
    </p>

    <div class="mt-3 space-y-3">
        @foreach ($labels as $zone => $label)
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end border rounded-md p-3">
                <div class="text-sm font-medium text-gray-700">{{ $label }}</div>
                <div>
                    <label class="block text-xs text-gray-500">Tarif per kg (Rp)</label>
                    <input type="number" min="0" name="rates[{{ $zone }}][price_per_kg]"
                           value="{{ old("rates.$zone.price_per_kg", $existing->get($zone)?->price_per_kg) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                    @error("rates.$zone.price_per_kg") <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs text-gray-500">Estimasi (mis. 2-3 hari)</label>
                    <input type="text" maxlength="30" name="rates[{{ $zone }}][estimated_days]"
                           value="{{ old("rates.$zone.estimated_days", $existing->get($zone)?->estimated_days) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                    @error("rates.$zone.estimated_days") <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        @endforeach
    </div>
</div>

<div>
    <input type="hidden" name="is_active" value="0">
    <label class="inline-flex items-center">
        <input type="checkbox" name="is_active" value="1"
               class="rounded border-gray-300 text-indigo-600 shadow-sm"
               @checked(old('is_active', $method?->is_active ?? true))>
        <span class="ms-2 text-sm text-gray-600">Aktif (tampil di checkout)</span>
    </label>
</div>
