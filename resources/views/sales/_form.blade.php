{{--
    Partial form Sale — dipakai di create & edit.
    Variabel:
      $categories : Collection<Category> dengan sales_sum_qty (via withSum)
      $sale       : (opsional, hanya di edit) instance Sale
--}}

@php
    $isEdit = isset($sale);

    // Bangun data kategori untuk JS
    $catData = $categories->map(fn ($c) => [
        'id'      => $c->id,
        'name'    => $c->name,
        'price'   => $c->price,
        'quota'   => $c->quota,
        'terjual' => (int) ($c->sales_sum_qty ?? 0),
        'sisa'    => $c->quota > 0 ? max(0, $c->quota - (int) ($c->sales_sum_qty ?? 0)) : null,
    ])->keyBy('id');

    // Pada edit: tambahkan kembali qty lama ke sisa kuota kategori yang sama
    if ($isEdit && $catData->has($sale->category_id)) {
        $item = $catData[$sale->category_id];
        if ($item['quota'] > 0) {
            $item['sisa'] = max(0, $item['sisa'] + $sale->qty);
            $catData[$sale->category_id] = $item;
        }
    }

    $selectedCatId = old('category_id', $isEdit ? $sale->category_id : ($categories->first()?->id));
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    {{-- Waktu (read-only) --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Pembelian</label>
        <div class="border rounded p-2 bg-gray-50 text-gray-600 flex items-center gap-2">
            @if($isEdit)
                <span>{{ $sale->purchased_at->format('d/m/Y H:i:s') }} WIB</span>
                <span class="text-xs text-gray-400">(waktu asli transaksi, tidak dapat diubah)</span>
            @else
                <span id="live_clock" class="font-mono font-medium">—</span>
                <span class="text-xs text-gray-400">WIB · otomatis saat simpan</span>
            @endif
        </div>
    </div>

    {{-- Kategori --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Kategori <span class="text-red-500">*</span>
        </label>
        <select id="form_category_id" name="category_id"
                class="w-full border rounded p-2 focus:outline-none focus:ring-2 focus:ring-gray-400"
                required>
            <option value="">-- Pilih Kategori --</option>
            @foreach($categories as $c)
                @php
                    $item  = $catData[$c->id];
                    $habis = $c->quota > 0 && $item['sisa'] === 0;
                    // Pada create: disable option HABIS; pada edit jangan disable
                    $disableOpt = !$isEdit && $habis;
                @endphp
                <option value="{{ $c->id }}"
                    @selected($selectedCatId == $c->id)
                    @if($disableOpt) disabled @endif>
                    {{ $c->name }} — Rp {{ number_format($c->price, 0, ',', '.') }}
                    @if($c->quota > 0)
                        (sisa {{ $item['sisa'] }}){{ $habis ? ' — HABIS' : '' }}
                    @else
                        (tanpa batas)
                    @endif
                </option>
            @endforeach
        </select>
    </div>

    {{-- Harga kategori (read-only via JS) --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Harga Kategori</label>
        <div id="form_price_display"
             class="border rounded p-2 bg-gray-50 text-gray-700 font-medium">—</div>
    </div>

    {{-- Qty --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Jumlah (Qty) <span class="text-red-500">*</span>
        </label>
        <input id="form_qty" name="qty" type="number" min="1"
               value="{{ old('qty', $isEdit ? $sale->qty : 1) }}"
               class="w-full border rounded p-2 focus:outline-none focus:ring-2 focus:ring-gray-400"
               required>
        <p id="form_sisa_info" class="text-xs mt-1 text-gray-500"></p>
    </div>

    {{-- Subtotal (read-only via JS) --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Subtotal</label>
        <div id="form_subtotal_display"
             class="border rounded p-2 bg-gray-50 text-gray-700 font-medium">—</div>
    </div>

    {{-- Catatan --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
        <input id="form_note" name="note"
               value="{{ old('note', $isEdit ? $sale->note : '') }}"
               placeholder="Opsional"
               class="w-full border rounded p-2 focus:outline-none focus:ring-2 focus:ring-gray-400"
               maxlength="500">
    </div>
</div>

<script>
(function () {
    const IS_EDIT   = {{ $isEdit ? 'true' : 'false' }};
    const catData   = @json($catData);
    const selEl     = document.getElementById('form_category_id');
    const qtyEl     = document.getElementById('form_qty');
    const priceDisp = document.getElementById('form_price_display');
    const subDisp   = document.getElementById('form_subtotal_display');
    const sisaInfo  = document.getElementById('form_sisa_info');
    const clockEl   = document.getElementById('live_clock');

    // ── Live clock (hanya di create) ──────────────────────────────────────
    if (clockEl) {
        function tickClock() {
            const now = new Date();
            // Format DD/MM/YYYY HH:MM:SS
            const pad = n => String(n).padStart(2, '0');
            clockEl.textContent =
                pad(now.getDate())    + '/' +
                pad(now.getMonth()+1) + '/' +
                now.getFullYear()     + ' ' +
                pad(now.getHours())   + ':' +
                pad(now.getMinutes()) + ':' +
                pad(now.getSeconds());
        }
        tickClock();
        setInterval(tickClock, 1000);
    }

    // ── Format rupiah ─────────────────────────────────────────────────────
    function fmt(n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID');
    }

    // ── Update harga / subtotal / sisa info ──────────────────────────────
    function updateDisplay() {
        const id  = selEl.value;
        const qty = parseInt(qtyEl.value) || 0;
        const cat = catData[id];

        if (!cat) {
            priceDisp.textContent = '—';
            subDisp.textContent   = '—';
            sisaInfo.textContent  = '';
            return;
        }

        priceDisp.textContent = fmt(cat.price);
        subDisp.textContent   = fmt(cat.price * qty);

        if (cat.quota > 0) {
            const sisa = cat.sisa;
            if (sisa === 0) {
                sisaInfo.textContent = '⚠ Kuota habis.';
                sisaInfo.className   = 'text-xs mt-1 text-red-600 font-semibold';
            } else if (qty > sisa) {
                sisaInfo.textContent = `⚠ Melebihi sisa kuota (${sisa}).`;
                sisaInfo.className   = 'text-xs mt-1 text-red-600 font-semibold';
            } else {
                sisaInfo.textContent = `Sisa kuota: ${sisa}`;
                sisaInfo.className   = 'text-xs mt-1 text-gray-500';
            }
        } else {
            sisaInfo.textContent = 'Tanpa batas kuota.';
            sisaInfo.className   = 'text-xs mt-1 text-gray-400';
        }
    }

    selEl.addEventListener('change', updateDisplay);
    qtyEl.addEventListener('input',  updateDisplay);
    updateDisplay();

    // ── Expose updateDisplay + catData untuk dipakai halaman create ───────
    window._formSale = { catData, updateDisplay, selEl, qtyEl };
})();
</script>
