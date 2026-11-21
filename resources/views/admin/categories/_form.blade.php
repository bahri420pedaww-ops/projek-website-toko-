{{-- PLACEHOLDER (backend). Variabel: $parents, opsional $category --}}
@php $category = $category ?? null; @endphp

<div>
    <x-input-label for="name" value="Nama Kategori" />
    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                  :value="old('name', $category?->name)" required autofocus />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div>
    <x-input-label for="parent_id" value="Kategori Induk (opsional)" />
    <select id="parent_id" name="parent_id"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option value="">- Tidak ada (kategori utama) -</option>
        @foreach ($parents as $parent)
            <option value="{{ $parent->id }}" @selected(old('parent_id', $category?->parent_id) == $parent->id)>
                {{ $parent->name }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('parent_id')" class="mt-2" />
</div>

<div>
    <input type="hidden" name="is_active" value="0">
    <label class="inline-flex items-center">
        <input type="checkbox" name="is_active" value="1"
               class="rounded border-gray-300 text-indigo-600 shadow-sm"
               @checked(old('is_active', $category?->is_active ?? true))>
        <span class="ms-2 text-sm text-gray-600">Aktif</span>
    </label>
</div>
