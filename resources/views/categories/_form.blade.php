{{-- Partial form untuk Category: dipakai di create & edit --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="md:col-span-3">
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori <span class="text-red-500">*</span></label>
        <input name="name"
               value="{{ old('name', $category->name ?? '') }}"
               placeholder="mis. Tiket Umum, Tiket VIP"
               class="w-full border rounded p-2 focus:outline-none focus:ring-2 focus:ring-gray-400"
               required maxlength="100">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp) <span class="text-red-500">*</span></label>
        <input name="price" type="number" min="0"
               value="{{ old('price', $category->price ?? '') }}"
               placeholder="0"
               class="w-full border rounded p-2 focus:outline-none focus:ring-2 focus:ring-gray-400"
               required>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Kuota <span class="text-gray-400 text-xs">(kosong atau 0 = tanpa batas)</span></label>
        <input name="quota" type="number" min="0"
               value="{{ old('quota', $category->quota ?? '') }}"
               placeholder="0"
               class="w-full border rounded p-2 focus:outline-none focus:ring-2 focus:ring-gray-400">
        @isset($terjual)
            <p class="text-xs text-gray-500 mt-1">Sudah terjual: {{ $terjual }}. Kuota baru tidak boleh lebih kecil dari ini.</p>
        @endisset
    </div>
</div>
