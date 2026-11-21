{{-- PLACEHOLDER (backend). Variabel: $categories, opsional $product --}}
@php $product = $product ?? null; @endphp

<div>
    <x-input-label for="category_id" value="Kategori" />
    <select id="category_id" name="category_id" required
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option value="">- Pilih kategori -</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected(old('category_id', $product?->category_id) == $category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
</div>

<div>
    <x-input-label for="name" value="Nama Produk" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                  :value="old('name', $product?->name)" required />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div>
    <x-input-label for="description" value="Deskripsi" />
    <textarea id="description" name="description" rows="4"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $product?->description) }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
    <div>
        <x-input-label for="price" value="Harga (Rp)" />
        <x-text-input id="price" name="price" type="number" min="0" class="mt-1 block w-full"
                      :value="old('price', $product?->price)" required />
        <x-input-error :messages="$errors->get('price')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="stock" value="Stok" />
        <x-text-input id="stock" name="stock" type="number" min="0" class="mt-1 block w-full"
                      :value="old('stock', $product?->stock ?? 0)" required />
        <x-input-error :messages="$errors->get('stock')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="weight_gram" value="Berat (gram)" />
        <x-text-input id="weight_gram" name="weight_gram" type="number" min="0" class="mt-1 block w-full"
                      :value="old('weight_gram', $product?->weight_gram ?? 0)" required />
        <x-input-error :messages="$errors->get('weight_gram')" class="mt-2" />
    </div>
</div>

<div>
    <x-input-label for="images" value="Gambar (maksimal 5, JPG/PNG/WEBP, 2 MB per file)" />
    <input id="images" name="images[]" type="file" multiple accept="image/*"
           class="mt-1 block w-full text-sm text-gray-600">
    <x-input-error :messages="$errors->get('images')" class="mt-2" />
    <x-input-error :messages="$errors->get('images.*')" class="mt-2" />
    <p class="mt-1 text-xs text-gray-500">Gambar pertama yang diunggah otomatis menjadi gambar utama.</p>
</div>

<div>
    <input type="hidden" name="is_active" value="0">
    <label class="inline-flex items-center">
        <input type="checkbox" name="is_active" value="1"
               class="rounded border-gray-300 text-indigo-600 shadow-sm"
               @checked(old('is_active', $product?->is_active ?? true))>
        <span class="ms-2 text-sm text-gray-600">Tampilkan di toko (aktif)</span>
    </label>
</div>
