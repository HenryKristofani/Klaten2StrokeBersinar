@extends('layout')
@section('title', 'Riwayat Penghapusan')
@section('content')
<div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-2">
        <a href="{{ route('sales.index') }}" class="text-gray-500 hover:text-gray-800 text-sm">← Penjualan</a>
        <span class="text-gray-300">/</span>
        <h1 class="text-2xl font-bold">Riwayat Penghapusan</h1>
    </div>
    <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
        {{ $sales->total() }} transaksi dihapus
    </span>
</div>

<div class="bg-white rounded shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left">
        <tr>
            <th class="p-3">Waktu Pembelian</th>
            <th class="p-3">Kategori</th>
            <th class="p-3 text-right">Qty</th>
            <th class="p-3 text-right">Harga</th>
            <th class="p-3 text-right">Subtotal</th>
            <th class="p-3">Catatan</th>
            <th class="p-3">Alasan Hapus</th>
            <th class="p-3 whitespace-nowrap">Waktu Dihapus</th>
        </tr>
        </thead>
        <tbody>
        @forelse($sales as $s)
            <tr class="border-t hover:bg-red-50">
                <td class="p-3 whitespace-nowrap text-gray-500">{{ $s->purchased_at->format('d/m/Y H:i') }}</td>
                <td class="p-3">{{ $s->category?->name ?? '—' }}</td>
                <td class="p-3 text-right">{{ $s->qty }}</td>
                <td class="p-3 text-right">Rp {{ number_format($s->price, 0, ',', '.') }}</td>
                <td class="p-3 text-right">Rp {{ number_format($s->qty * $s->price, 0, ',', '.') }}</td>
                <td class="p-3 text-gray-400 max-w-xs truncate">{{ $s->note ?: '—' }}</td>
                <td class="p-3 text-red-700 max-w-xs">{{ $s->delete_reason }}</td>
                <td class="p-3 whitespace-nowrap text-gray-500">{{ $s->deleted_at->format('d/m/Y H:i') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="p-8 text-center text-gray-400">
                    Belum ada transaksi yang dihapus.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@if($sales->hasPages())
    <div class="mt-4">{{ $sales->links() }}</div>
@endif
@endsection
