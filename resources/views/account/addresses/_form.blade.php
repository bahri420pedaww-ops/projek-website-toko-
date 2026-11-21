{{-- PLACEHOLDER (backend). Opsional: $address.
     Peta memakai Leaflet + OpenStreetMap (gratis, tanpa API key).
     Klik peta / geser pin / cari tempat / "Lokasi saya" mengisi provinsi, kota, kecamatan,
     kode pos, dan alamat jalan secara otomatis. Semua kolom tetap bisa diisi dan diubah manual.
     Field yang dikirim ke backend: label, receiver_name, receiver_phone, province, city,
     district, postal_code, street_address, latitude, longitude, is_default. --}}
@php $address = $address ?? null; @endphp

<div>
    <label for="label" class="block text-sm font-medium text-gray-700">Label alamat</label>
    <input id="label" name="label" type="text" required placeholder="Rumah, Kantor, dan sebagainya"
           value="{{ old('label', $address?->label) }}"
           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
    @error('label') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label for="receiver_name" class="block text-sm font-medium text-gray-700">Nama penerima</label>
        <input id="receiver_name" name="receiver_name" type="text" required
               value="{{ old('receiver_name', $address?->receiver_name ?? Auth::user()->name) }}"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
        @error('receiver_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="receiver_phone" class="block text-sm font-medium text-gray-700">Nomor HP penerima</label>
        <input id="receiver_phone" name="receiver_phone" type="text" required placeholder="081234567890"
               value="{{ old('receiver_phone', $address?->receiver_phone ?? Auth::user()->phone) }}"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
        @error('receiver_phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>

{{-- ===== Peta ===== --}}
<div class="space-y-2">
    <label class="block text-sm font-medium text-gray-700">Tandai lokasi di peta (opsional)</label>

    <div class="flex flex-wrap gap-2">
        <input id="map_search" type="text" placeholder="Cari tempat atau alamat..."
               class="flex-1 min-w-[12rem] border-gray-300 rounded-md shadow-sm text-sm">
        <button type="button" id="map_search_btn"
                class="px-3 py-2 bg-gray-800 text-white text-xs font-semibold rounded-md">Cari</button>
        <button type="button" id="map_locate_btn"
                class="px-3 py-2 bg-gray-200 text-gray-800 text-xs font-semibold rounded-md">Lokasi saya</button>
    </div>

    <div id="address_map" class="rounded-md border border-gray-300" style="height: 320px; z-index: 0;"></div>
    <p id="map_status" class="text-xs text-gray-500">
        Klik peta atau geser pin untuk mengisi alamat otomatis. Hasilnya bisa kamu periksa dan ubah.
    </p>

    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $address?->latitude) }}">
    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $address?->longitude) }}">
    @error('latitude') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
    @error('longitude') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
</div>

{{-- ===== Alamat (diisi manual atau otomatis dari peta) ===== --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
        <label for="province" class="block text-sm font-medium text-gray-700">Provinsi</label>
        <input id="province" name="province" type="text" required placeholder="Contoh: Sumatera Barat"
               value="{{ old('province', $address?->province) }}"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
        @error('province') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="city" class="block text-sm font-medium text-gray-700">Kota / Kabupaten</label>
        <input id="city" name="city" type="text" required placeholder="Contoh: Padang"
               value="{{ old('city', $address?->city) }}"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
        @error('city') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="district" class="block text-sm font-medium text-gray-700">Kecamatan (opsional)</label>
        <input id="district" name="district" type="text"
               value="{{ old('district', $address?->district) }}"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
        @error('district') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="postal_code" class="block text-sm font-medium text-gray-700">Kode pos (opsional)</label>
        <input id="postal_code" name="postal_code" type="text" inputmode="numeric" maxlength="5"
               value="{{ old('postal_code', $address?->postal_code) }}"
               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
        @error('postal_code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>
</div>

<div>
    <label for="street_address" class="block text-sm font-medium text-gray-700">Alamat lengkap</label>
    <textarea id="street_address" name="street_address" rows="3" required
              placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, patokan"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('street_address', $address?->street_address) }}</textarea>
    @error('street_address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
</div>

@if ($address?->is_default)
    <p class="text-sm text-green-700">Ini adalah alamat utama. Untuk mengganti, jadikan alamat lain sebagai utama.</p>
@else
    <div>
        <input type="hidden" name="is_default" value="0">
        <label class="inline-flex items-center">
            <input type="checkbox" name="is_default" value="1"
                   class="rounded border-gray-300 text-indigo-600 shadow-sm"
                   @checked(old('is_default'))>
            <span class="ms-2 text-sm text-gray-600">Jadikan alamat utama</span>
        </label>
    </div>
@endif

{{-- ===== Leaflet (CDN, versi dikunci) + skrip peta ===== --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const statusEl = document.getElementById('map_status');
    const fields = {
        province: document.getElementById('province'),
        city:     document.getElementById('city'),
        district: document.getElementById('district'),
        postal:   document.getElementById('postal_code'),
        street:   document.getElementById('street_address'),
    };

    const hasPoint = latInput.value !== '' && lngInput.value !== '';
    const start = hasPoint ? [parseFloat(latInput.value), parseFloat(lngInput.value)] : [-2.5, 118];

    const map = L.map('address_map').setView(start, hasPoint ? 16 : 5);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; kontributor OpenStreetMap'
    }).addTo(map);

    let marker = null;
    let lastAutoStreet = '';
    let controller = null;

    function placeMarker(lat, lng) {
        if (marker) {
            marker.setLatLng([lat, lng]);
            return;
        }
        marker = L.marker([lat, lng], { draggable: true }).addTo(map);
        marker.on('dragend', function () {
            const p = marker.getLatLng();
            choose(p.lat, p.lng, false);
        });
    }

    // Simpan titik, lalu minta alamatnya ke OpenStreetMap (Nominatim)
    function choose(lat, lng, center) {
        placeMarker(lat, lng);
        latInput.value = lat.toFixed(7);
        lngInput.value = lng.toFixed(7);
        if (center) {
            map.setView([lat, lng], Math.max(map.getZoom(), 16));
        }
        reverseGeocode(lat, lng);
    }

    function fillFrom(a) {
        fields.province.value = a.state || a.region || '';
        fields.city.value     = a.city || a.town || a.municipality || a.county || '';
        fields.district.value = a.city_district || a.suburb || a.subdistrict || a.village || '';
        fields.postal.value   = a.postcode || '';

        const street = [a.road, a.house_number].filter(Boolean).join(' ');
        // Jangan menimpa alamat jalan yang sudah diketik pembeli
        if (street && (fields.street.value.trim() === '' || fields.street.value === lastAutoStreet)) {
            fields.street.value = street;
            lastAutoStreet = street;
        }
    }

    async function reverseGeocode(lat, lng) {
        if (controller) controller.abort();
        controller = new AbortController();
        statusEl.textContent = 'Mengambil alamat dari peta...';
        try {
            const url = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&addressdetails=1'
                      + '&accept-language=id&lat=' + lat + '&lon=' + lng;
            const res = await fetch(url, { signal: controller.signal });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const data = await res.json();
            fillFrom(data.address || {});
            statusEl.textContent = 'Alamat terisi dari peta. Periksa dan lengkapi bila perlu.';
        } catch (e) {
            if (e.name === 'AbortError') return;
            statusEl.textContent = 'Gagal mengambil alamat otomatis. Titik peta tetap tersimpan, isi alamat secara manual.';
        }
    }

    async function searchPlace() {
        const q = document.getElementById('map_search').value.trim();
        if (!q) return;
        statusEl.textContent = 'Mencari lokasi...';
        try {
            const url = 'https://nominatim.openstreetmap.org/search?format=jsonv2&addressdetails=1&limit=1'
                      + '&countrycodes=id&accept-language=id&q=' + encodeURIComponent(q);
            const res = await fetch(url);
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const results = await res.json();
            if (!results.length) {
                statusEl.textContent = 'Lokasi tidak ditemukan. Coba kata kunci lain atau klik langsung di peta.';
                return;
            }
            const r = results[0];
            const lat = parseFloat(r.lat);
            const lng = parseFloat(r.lon);
            placeMarker(lat, lng);
            latInput.value = lat.toFixed(7);
            lngInput.value = lng.toFixed(7);
            map.setView([lat, lng], 17);
            fillFrom(r.address || {});
            statusEl.textContent = 'Lokasi ditemukan. Geser pin kalau kurang tepat, lalu periksa alamatnya.';
        } catch (e) {
            statusEl.textContent = 'Pencarian gagal. Coba lagi atau klik langsung di peta.';
        }
    }

    map.on('click', function (e) { choose(e.latlng.lat, e.latlng.lng, false); });

    document.getElementById('map_search_btn').addEventListener('click', searchPlace);
    document.getElementById('map_search').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); searchPlace(); }
    });

    document.getElementById('map_locate_btn').addEventListener('click', function () {
        if (!navigator.geolocation) {
            statusEl.textContent = 'Browser tidak mendukung deteksi lokasi. Klik langsung di peta.';
            return;
        }
        statusEl.textContent = 'Mencari lokasimu...';
        navigator.geolocation.getCurrentPosition(
            function (pos) { choose(pos.coords.latitude, pos.coords.longitude, true); },
            function () {
                statusEl.textContent = 'Lokasi tidak bisa diakses. Deteksi lokasi hanya berfungsi di localhost atau HTTPS, dan butuh izin browser. Klik langsung di peta sebagai gantinya.';
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    });

    // Alamat yang sudah punya titik: tampilkan pin tanpa menimpa isian yang tersimpan
    if (hasPoint) {
        placeMarker(start[0], start[1]);
    }
})();
</script>
