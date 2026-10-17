@extends('layout')
@section('title', 'Edit Penjualan')
@section('content')
<div class="flex items-center gap-2 mb-4">
    <a href="{{ route('sales.index') }}" class="text-gray-500 hover:text-gray-800 text-sm">← Kembali</a>
    <span class="text-gray-300">/</span>
    <h1 class="text-2xl font-bold">Edit Penjualan</h1>
</div>

<div class="bg-white rounded shadow p-6 max-w-3xl">
    <form method="POST" action="{{ route('sales.update', $sale) }}">
        @csrf @method('PUT')
        @include('sales._form')
        <div class="mt-6 flex gap-3">
            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition font-medium">
                Perbarui Penjualan
            </button>
            <a href="{{ route('sales.index') }}"
               class="border px-6 py-2 rounded hover:bg-gray-50 transition text-sm font-medium">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
