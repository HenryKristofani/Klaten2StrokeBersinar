@extends('layout')
@section('title', 'Kategori Tiket')
@section('content')
<div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">Kategori Tiket</h1>
    <a href="{{ route('categories.create') }}"
       class="bg-gray-900 text-white px-4 py-2 rounded hover:bg-gray-700 transition text-sm font-medium">
        + Tambah Kategori
    </a>
</div>

<div class="bg-white rounded shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left">
        <tr>
            <th class="p-3">Nama Kategori</th>
            <th class="p-3">Harga</th>
            <th class="p-3">Kuota</th>
            <th class="p-3">Terjual</th>
            <th class="p-3">Sisa Kuota</th>
            <th class="p-3"></th>
        </tr>
        </thead>
        <tbody>
        @forelse($categories as $c)
            @php
                $sisa = $c->quota > 0 ? max(0, $c->quota - ($c->sales_sum_qty ?? 0)) : null;
            @endphp
            <tr class="border-t hover:bg-gray-50">
                <td class="p-3 font-medium">{{ $c->name }}</td>
                <td class="p-3">Rp {{ number_format($c->price, 0, ',', '.') }}</td>
                <td class="p-3">
                    @if($c->quota > 0)
                        {{ number_format($c->quota, 0, ',', '.') }}
                    @else
                        <span class="text-gray-400">Tanpa batas</span>
                    @endif
                </td>
                <td class="p-3">{{ number_format($c->sales_sum_qty ?? 0, 0, ',', '.') }}</td>
                <td class="p-3">
                    @if($c->quota > 0)
                        <span class="{{ $sisa == 0 ? 'text-red-600 font-semibold' : 'text-green-700' }}">
                            {{ number_format($sisa, 0, ',', '.') }}
                        </span>
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </td>
                <td class="p-3">
                    <div class="flex gap-2 justify-end">
                        <a href="{{ route('categories.edit', $c) }}"
                           class="text-blue-600 hover:underline text-sm">Edit</a>
                        <form method="POST" action="{{ route('categories.destroy', $c) }}"
                              onsubmit="return confirm('Hapus kategori \'{{ addslashes($c->name) }}\' beserta seluruh penjualannya ({{ $c->sales_sum_qty ?? 0 }} tiket)? Tindakan ini tidak dapat dibatalkan.')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:underline text-sm">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="p-6 text-center text-gray-400">Belum ada kategori.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
