@extends('layout')
@section('title', 'Rekap Penjualan')
@section('content')
<h1 class="text-2xl font-bold mb-4">Rekap Penjualan</h1>

<div class="grid grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded shadow p-4">
        <div class="text-sm text-gray-500">Total Income</div>
        <div class="text-3xl font-bold text-green-700">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
    </div>
    <div class="bg-white rounded shadow p-4">
        <div class="text-sm text-gray-500">Total Item Terjual</div>
        <div class="text-3xl font-bold">{{ number_format($totalQty, 0, ',', '.') }}</div>
    </div>
</div>

<div class="bg-white rounded shadow overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left">
        <tr>
            <th class="p-3">Kategori</th>
            <th class="p-3 text-right">Harga</th>
            <th class="p-3 text-right">Terjual</th>
            <th class="p-3">Kuota</th>
            <th class="p-3 text-right">Income</th>
            <th class="p-3"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($rekap as $c)
            <tr class="border-t hover:bg-gray-50">
                <td class="p-3 font-medium">{{ $c->name }}</td>
                <td class="p-3 text-right">Rp {{ number_format($c->price, 0, ',', '.') }}</td>
                <td class="p-3 text-right">{{ number_format($c->total_qty, 0, ',', '.') }}</td>
                <td class="p-3">
                    @if($c->quota > 0)
                        {{ $c->total_qty }}/{{ $c->quota }}
                        <span class="text-gray-500">(sisa {{ max(0, $c->quota - $c->total_qty) }})</span>
                    @else
                        <span class="text-gray-400">Tanpa batas</span>
                    @endif
                </td>
                <td class="p-3 text-right text-green-700 font-medium">Rp {{ number_format($c->income, 0, ',', '.') }}</td>
                <td class="p-3 text-right">
                    <a class="text-blue-600 hover:underline text-sm"
                       href="{{ route('sales.index', ['category' => $c->id]) }}">Detail</a>
                </td>
            </tr>
        @endforeach
        </tbody>
        <tfoot class="bg-gray-50 font-bold border-t">
        <tr>
            <td class="p-3" colspan="2">TOTAL</td>
            <td class="p-3 text-right">{{ number_format($totalQty, 0, ',', '.') }}</td>
            <td class="p-3"></td>
            <td class="p-3 text-right text-green-700">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
            <td></td>
        </tr>
        </tfoot>
    </table>
</div>
@endsection