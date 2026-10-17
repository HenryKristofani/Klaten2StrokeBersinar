@extends('layout')
@section('title', 'Catat Penjualan')
@section('content')

{{-- Toast notification --}}
<div id="toast"
     class="fixed top-4 right-4 z-50 hidden max-w-sm px-5 py-3 rounded-lg shadow-lg text-white text-sm font-medium transition-all duration-300">
</div>

<div class="flex items-center gap-2 mb-4">
    <a href="{{ route('sales.index') }}" class="text-gray-500 hover:text-gray-800 text-sm">← Kembali</a>
    <span class="text-gray-300">/</span>
    <h1 class="text-2xl font-bold">Catat Penjualan</h1>
</div>

{{-- Error banner (AJAX validation errors) --}}
<div id="error_banner" class="hidden bg-red-100 border border-red-300 text-red-800 p-3 rounded mb-4 text-sm"></div>

<div class="bg-white rounded shadow p-6 max-w-3xl">
    <form id="sale_form" method="POST" action="{{ route('sales.store') }}">
        @csrf
        @include('sales._form')
        <div class="mt-6 flex gap-3">
            <button id="btn_submit" type="submit"
                    class="bg-gray-900 text-white px-6 py-2 rounded hover:bg-gray-700 transition font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                Catat Penjualan
            </button>
            <a href="{{ route('sales.index') }}"
               class="border px-6 py-2 rounded hover:bg-gray-50 transition text-sm font-medium">
                Kembali ke Daftar
            </a>
        </div>
    </form>
</div>

<script>
(function () {
    const form      = document.getElementById('sale_form');
    const btnSubmit = document.getElementById('btn_submit');
    const errBanner = document.getElementById('error_banner');
    const toast     = document.getElementById('toast');
    let toastTimer  = null;

    // ── Toast helper ──────────────────────────────────────────────────────
    function showToast(msg, isError = false) {
        clearTimeout(toastTimer);
        toast.textContent  = msg;
        toast.className    = [
            'fixed top-4 right-4 z-50 max-w-sm px-5 py-3 rounded-lg shadow-lg text-white text-sm font-medium transition-all duration-300',
            isError ? 'bg-red-600' : 'bg-green-600',
        ].join(' ');
        toastTimer = setTimeout(() => {
            toast.classList.add('hidden');
        }, 3000);
    }

    // ── Update dropdown options after success ─────────────────────────────
    function applyNewSisa(sisaMap) {
        const { catData, selEl, updateDisplay } = window._formSale;

        // Update catData in-place
        Object.entries(sisaMap).forEach(([id, sisa]) => {
            if (catData[id]) {
                catData[id].sisa = sisa;   // null = tanpa batas
            }
        });

        // Re-render dropdown options text + disabled state
        Array.from(selEl.options).forEach(opt => {
            const id  = opt.value;
            const cat = catData[id];
            if (!cat) return;

            const habis = cat.quota > 0 && cat.sisa === 0;
            opt.disabled = habis;

            if (cat.quota > 0) {
                opt.textContent = `${cat.name} — Rp ${Number(cat.price).toLocaleString('id-ID')} (sisa ${cat.sisa})${habis ? ' — HABIS' : ''}`;
            } else {
                opt.textContent = `${cat.name} — Rp ${Number(cat.price).toLocaleString('id-ID')} (tanpa batas)`;
            }
        });

        // Refresh price/sisa display below qty
        updateDisplay();
    }

    // ── AJAX submit ───────────────────────────────────────────────────────
    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        // Disable button to prevent double-submit
        btnSubmit.disabled = true;
        btnSubmit.textContent = 'Menyimpan…';
        errBanner.classList.add('hidden');

        const formData = new FormData(form);

        try {
            const res = await fetch(form.action, {
                method:  'POST',
                headers: {
                    'Accept':           'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: formData,
            });

            const json = await res.json();

            if (res.status === 201) {
                // ✅ Sukses
                showToast('✓ ' + json.message);

                // Reset qty dan catatan, pertahankan kategori
                window._formSale.qtyEl.value = 1;
                const noteEl = document.getElementById('form_note');
                if (noteEl) noteEl.value = '';

                // Update sisa kuota di dropdown dan info
                applyNewSisa(json.sisa_kuota);

            } else if (res.status === 422) {
                // ❌ Validasi gagal
                const msgs = Object.values(json.errors).flat();
                errBanner.innerHTML = '<strong>Terdapat kesalahan:</strong><br>' +
                    msgs.map(m => '• ' + m).join('<br>');
                errBanner.classList.remove('hidden');
                errBanner.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

            } else {
                showToast('Terjadi kesalahan. Coba lagi.', true);
            }

        } catch (err) {
            showToast('Gagal terhubung ke server.', true);
            console.error(err);
        } finally {
            btnSubmit.disabled = false;
            btnSubmit.textContent = 'Catat Penjualan';
        }
    });
})();
</script>
@endsection
