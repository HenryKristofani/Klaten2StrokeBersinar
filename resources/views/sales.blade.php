@extends('layout')
@section('content')
<h1 class="text-2xl font-bold mb-4">Penjualan</h1>

<form method="POST" action="{{ route('sales.store') }}" class="bg-white rounded shadow p-4 mb-6 grid grid-cols-1 md:grid-cols-3 gap-3">
    @csrf
    <select name="category_id" class="border rounded p-2" required>
        @foreach($categories as $c)
            <option value="{{ $c->id }}" @selected(old('category_id', $filter) == $c->id)>
                {{ $c->name }} (Rp {{ number_format($c->price, 0, ',', '.') }})
            </option>
        @endforeach
    </select>
    <input name="buyer_name" value="{{ old('buyer_name') }}" placeholder="Nama pembeli" class="border rounded p-2" required>
    <input name="qty" type="number" min="1" value="{{ old('qty', 1) }}" class="border rounded p-2" required>
    <input name="price" type="number" min="0" value="{{ old('price') }}" placeholder="Harga (kosong = harga kategori)" class="border rounded p-2">
    <input name="purchased_at" type="datetime-local" value="{{ old('purchased_at', now()->format('Y-m-d\TH:i')) }}" class="border rounded p-2" required>
    <input name="note" value="{{ old('note') }}" placeholder="Catatan" class="border rounded p-2">
    <button class="bg-gray-900 text-white rounded p-2 md:col-span-3">Catat Penjualan</button>
</form>

<form method="GET" class="mb-3 flex gap-2 items-center">
    <label class="text-sm">Filter:</label>
    <select name="category" onchange="this.form.submit()" class="border rounded p-2">
        <option value="">Semua kategori</option>
        @foreach($categories as $c)
            <option value="{{ $c->id }}" @selected($filter == $c->id)>{{ $c->name }}</option>
        @endforeach
    </select>
    <span class="ml-auto text-sm">
        {{ $sumQty }} item · <b>Rp {{ number_format($sumIncome, 0, ',', '.') }}</b>
    </span>
</form>

<div class="bg-white rounded shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left">
        <tr>
            <th class="p-3">Waktu</th>
            <th class="p-3">Pembeli</th>
            <th class="p-3">Kategori</th>
            <th class="p-3">Qty</th>
            <th class="p-3">Harga</th>
            <th class="p-3 text-right">Subtotal</th>
            <th class="p-3">Catatan</th>
            <th class="p-3"></th>
        </tr>
        </thead>
        <tbody>
        @forelse($sales as $s)
            <tr class="border-t">
                <td class="p-3 whitespace-nowrap">{{ $s->purchased_at->format('d/m/Y H:i') }}</td>
                <td class="p-3">{{ $s->buyer_name }}</td>
                <td class="p-3">{{ $s->category->name }}</td>
                <td class="p-3">{{ $s->qty }}</td>
                <td class="p-3">Rp {{ number_format($s->price, 0, ',', '.') }}</td>
                <td class="p-3 text-right">Rp {{ number_format($s->qty * $s->price, 0, ',', '.') }}</td>
                <td class="p-3">{{ $s->note }}</td>
                <td class="p-3">
                    <form method="POST" action="{{ route('sales.destroy', $s) }}" onsubmit="return confirm('Hapus?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:underline">Hapus</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="p-6 text-center text-gray-400">Belum ada penjualan</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection