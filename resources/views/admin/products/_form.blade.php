<div>
    <label for="name" class="block text-sm font-semibold text-slate-700">Nama Produk</label>
    <input id="name" name="name" value="{{ old('name', $product->name) }}" required maxlength="255"
           class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50">
</div>

<div>
    <label for="slug" class="block text-sm font-semibold text-slate-700">Slug <span class="font-normal text-slate-400">(opsional)</span></label>
    <input id="slug" name="slug" value="{{ old('slug', $product->slug) }}" maxlength="255"
           class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50">
</div>

<div>
    <label for="category_id" class="block text-sm font-semibold text-slate-700">Kategori</label>
    <select id="category_id" name="category_id" required
            class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50">
        <option value="">Pilih kategori</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
        @endforeach
    </select>
</div>

<div>
    <label for="description" class="block text-sm font-semibold text-slate-700">Deskripsi</label>
    <textarea id="description" name="description" rows="5" maxlength="10000"
              class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50">{{ old('description', $product->description) }}</textarea>
</div>

<div class="grid gap-5 sm:grid-cols-3">
    <div>
        <label for="price" class="block text-sm font-semibold text-slate-700">Harga (Rp)</label>
        <input id="price" type="number" name="price" value="{{ old('price', $product->price) }}" min="0" required
               class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50">
    </div>
    <div>
        <label for="stock" class="block text-sm font-semibold text-slate-700">Stok</label>
        <input id="stock" type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" min="0" required
               class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50">
    </div>
    <div>
        <label for="weight_gram" class="block text-sm font-semibold text-slate-700">Berat (gram)</label>
        <input id="weight_gram" type="number" name="weight_gram" value="{{ old('weight_gram', $product->weight_gram ?? 0) }}" min="0" required
               class="mt-2 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-50">
    </div>
</div>

<div>
    <label for="image" class="block text-sm font-semibold text-slate-700">Gambar Utama Produk</label>
    @if ($product->primaryImage)
        <div class="mt-3 flex items-center gap-4">
            <img src="{{ \Illuminate\Support\Str::startsWith($product->primaryImage->path, ['http://', 'https://']) ? $product->primaryImage->path : asset('storage/' . $product->primaryImage->path) }}" alt="{{ $product->name }}" class="h-20 w-20 rounded-xl border border-slate-200 object-cover">
            <p class="text-sm text-slate-500">Gambar saat ini. Pilih file baru untuk menggantinya.</p>
        </div>
    @endif
    <input id="image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" {{ $product->exists ? '' : 'required' }}
           class="mt-2 block w-full cursor-pointer rounded-xl border border-slate-200 bg-white text-sm text-slate-600 file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:font-semibold file:text-slate-700 hover:file:bg-slate-200">
    <p class="mt-2 text-xs text-slate-500">Format JPG, PNG, atau WEBP. Maksimal 5 MB.</p>
</div>

<label class="inline-flex items-center gap-3 rounded-xl bg-slate-50 px-4 py-3">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->exists ? $product->is_active : true))
           class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
    <span class="text-sm font-medium text-slate-700">Tampilkan produk di katalog</span>
</label>
