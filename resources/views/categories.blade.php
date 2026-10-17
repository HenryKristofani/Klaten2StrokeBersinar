@extends('layout')
@section('content')
<h1 class="text-2xl font-bold mb-4">Kategori Tiket</h1>

<form method="POST" action="{{ route('categories.store') }}" class="bg-white rounded shadow p-4 mb-6 grid grid-cols-1 md:grid-cols-4 gap-3">
    @csrf
    <input name="name" placeholder="Nama (mis. Tiket Umum)" class="border rounded p-2" required>
    <input name="price" type="number" min="0" placeholder="Harga" class="border rounded p-2" required>
    <input name="quota" type="number" min="0" placeholder="Kuota (0 = tanpa batas)" class="border rounded p-2">
    <button class="bg-gray-900 text-white rounded p-2">Tambah Kategori</button>
</form>

<div class="bg-white rounded shadow divide-y">
    @foreach($categories as $c)
        <div class="p-3 flex flex-col md:flex-row gap-2 md:items-center">
            <form method="POST" action="{{ route('categories.update', $c) }}" class="flex flex-1 flex-col md:flex-row gap-2">
                @csrf @method('PUT')
                <input name="name" value="{{ $c->name }}" class="border rounded p-2 flex-1" required>
                <input name="price" type="number" min="0" value="{{ $c->price }}" class="border rounded p-2 md:w-36" required>
                <input name="quota" type="number" min="0" value="{{ $c->quota }}" class="border rounded p-2 md:w-28">
                <button class="bg-blue-600 text-white rounded px-3 py-2">Simpan</button>
            </form>
            <form method="POST" action="{{ route('categories.destroy', $c) }}"
                  onsubmit="return confirm('Hapus kategori beserta seluruh penjualannya?')">
                @csrf @method('DELETE')
                <button class="bg-red-600 text-white rounded px-3 py-2">Hapus</button>
            </form>
        </div>
    @endforeach
</div>
@endsection