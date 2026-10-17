@extends('layout')
@section('title', 'Tambah Kategori')
@section('content')
<div class="flex items-center gap-2 mb-4">
    <a href="{{ route('categories.index') }}" class="text-gray-500 hover:text-gray-800 text-sm">← Kembali</a>
    <span class="text-gray-300">/</span>
    <h1 class="text-2xl font-bold">Tambah Kategori</h1>
</div>

<div class="bg-white rounded shadow p-6 max-w-2xl">
    <form method="POST" action="{{ route('categories.store') }}">
        @csrf
        @include('categories._form')
        <div class="mt-6 flex gap-3">
            <button type="submit"
                    class="bg-gray-900 text-white px-6 py-2 rounded hover:bg-gray-700 transition font-medium">
                Simpan Kategori
            </button>
            <a href="{{ route('categories.index') }}"
               class="border px-6 py-2 rounded hover:bg-gray-50 transition text-sm font-medium">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
