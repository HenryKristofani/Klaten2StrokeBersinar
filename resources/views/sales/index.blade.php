@extends('layout')
@section('title', 'Penjualan')
@section('content')

{{-- ── Modal Hapus ──────────────────────────────────────────────────────── --}}
<div id="delete_modal"
     class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-1">Hapus Transaksi</h2>
        <p id="modal_desc" class="text-sm text-gray-500 mb-4"></p>

        <form id="delete_form" method="POST">
            @csrf @method('DELETE')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Alasan Penghapusan <span class="text-red-500">*</span>
                </label>
                <textarea id="delete_reason" name="delete_reason" rows="3"
                          placeholder="Wajib diisi. Contoh: Tiket dibatalkan oleh pembeli, kesalahan input, dsb."
                          class="w-full border rounded-lg p-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 resize-none"
                          required maxlength="500"></textarea>
                <p id="modal_error" class="text-xs text-red-600 mt-1 hidden">Alasan penghapusan wajib diisi.</p>
            </div>
            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 bg-red-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition">
                    Ya, Hapus Transaksi
                </button>
                <button type="button" onclick="closeDeleteModal()"
                        class="flex-1 border py-2 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── Header ──────────────────────────────────────────────────────────── --}}
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Penjualan</h1>
    <a href="{{ route('sales.create') }}"
       class="bg-gray-900 text-white px-4 py-2 rounded hover:bg-gray-700 transition text-sm font-medium">
        + Catat Penjualan
    </a>
</div>

{{-- ── Filter ──────────────────────────────────────────────────────────── --}}
<form method="GET" class="bg-white rounded shadow p-4 mb-4">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Kategori</label>
            <select name="category" class="w-full border rounded p-2 text-sm">
                <option value="">Semua Kategori</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" @selected($filter == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Cari Catatan</label>
            <input name="catatan" value="{{ $catatan }}" placeholder="Kata kunci catatan..."
                   class="w-full border rounded p-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Dari Tanggal</label>
            <input name="dari" type="date" value="{{ $dari }}" class="w-full border rounded p-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Sampai Tanggal</label>
            <input name="sampai" type="date" value="{{ $sampai }}" class="w-full border rounded p-2 text-sm">
        </div>
    </div>
    <div class="mt-3 flex gap-2 items-center">
        <button type="submit"
                class="bg-gray-900 text-white px-4 py-1.5 rounded text-sm hover:bg-gray-700 transition">
            Filter
        </button>
        <a href="{{ route('sales.index') }}"
           class="border px-4 py-1.5 rounded text-sm hover:bg-gray-50 transition">
            Reset
        </a>
        <span class="ml-auto text-sm text-gray-600">
            Total: <strong>{{ number_format($sumQty, 0, ',', '.') }}</strong> item ·
            <strong>Rp {{ number_format($sumIncome, 0, ',', '.') }}</strong>
        </span>
    </div>
</form>

{{-- ── Tabel ───────────────────────────────────────────────────────────── --}}
<div class="bg-white rounded shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left">
        <tr>
            <th class="p-3">Waktu</th>
            <th class="p-3">Kategori</th>
            <th class="p-3 text-right">Qty</th>
            <th class="p-3 text-right">Harga</th>
            <th class="p-3 text-right">Subtotal</th>
            <th class="p-3">Catatan</th>
            <th class="p-3"></th>
        </tr>
        </thead>
        <tbody>
        @forelse($sales as $s)
            <tr class="border-t hover:bg-gray-50">
                <td class="p-3 whitespace-nowrap">{{ $s->purchased_at->format('d/m/Y H:i') }}</td>
                <td class="p-3">{{ $s->category->name }}</td>
                <td class="p-3 text-right">{{ $s->qty }}</td>
                <td class="p-3 text-right">Rp {{ number_format($s->price, 0, ',', '.') }}</td>
                <td class="p-3 text-right font-medium">Rp {{ number_format($s->qty * $s->price, 0, ',', '.') }}</td>
                <td class="p-3 text-gray-500 max-w-xs truncate">{{ $s->note }}</td>
                <td class="p-3">
                    <div class="flex gap-2 justify-end items-center">
                        <a href="{{ route('sales.edit', $s) }}"
                           class="text-blue-600 hover:underline text-sm">Edit</a>
                        <button type="button"
                                onclick="openDeleteModal(
                                    '{{ route('sales.destroy', $s) }}',
                                    '{{ $s->category->name }}',
                                    {{ $s->qty }},
                                    '{{ $s->purchased_at->format('d/m/Y H:i') }}'
                                )"
                                class="text-red-600 hover:underline text-sm">
                            Hapus
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="p-6 text-center text-gray-400">Belum ada data penjualan.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
@if($sales->hasPages())
    <div class="mt-4">{{ $sales->links() }}</div>
@endif

<script>
function openDeleteModal(actionUrl, catName, qty, waktu) {
    const modal    = document.getElementById('delete_modal');
    const form     = document.getElementById('delete_form');
    const desc     = document.getElementById('modal_desc');
    const reasonEl = document.getElementById('delete_reason');
    const errEl    = document.getElementById('modal_error');

    form.action    = actionUrl;
    reasonEl.value = '';
    errEl.classList.add('hidden');
    desc.textContent = `${catName} · ${qty} tiket · ${waktu} — menghapus akan mengembalikan ${qty} kuota ke kategori ini.`;

    modal.classList.remove('hidden');
    setTimeout(() => reasonEl.focus(), 50);
}

function closeDeleteModal() {
    document.getElementById('delete_modal').classList.add('hidden');
}

// Validasi alasan sebelum submit modal
document.getElementById('delete_form').addEventListener('submit', function (e) {
    const reasonEl = document.getElementById('delete_reason');
    const errEl    = document.getElementById('modal_error');
    if (!reasonEl.value.trim()) {
        e.preventDefault();
        errEl.classList.remove('hidden');
        reasonEl.focus();
    }
});

// Tutup modal saat klik backdrop
document.getElementById('delete_modal').addEventListener('click', function (e) {
    if (e.target === this) closeDeleteModal();
});

// Tutup modal dengan Escape
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeDeleteModal();
});
</script>
@endsection
