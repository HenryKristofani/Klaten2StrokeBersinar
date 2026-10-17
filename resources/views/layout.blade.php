<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Ticketing') — 2 Stroke Meet Up</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen">
<nav class="bg-gray-900 text-white shadow-md">
    <div class="max-w-6xl mx-auto px-4 py-3 flex gap-1 items-center">
        <span class="font-bold mr-4 text-yellow-400">2 Stroke Meet Up</span>
        @php
            $current = request()->route()->getName();
        @endphp
        <a href="{{ route('recap') }}"
           class="px-3 py-1.5 rounded text-sm font-medium transition
                  {{ $current === 'recap' ? 'bg-yellow-400 text-gray-900' : 'hover:bg-gray-700' }}">
            Rekap
        </a>
        <a href="{{ route('sales.index') }}"
           class="px-3 py-1.5 rounded text-sm font-medium transition
                  {{ in_array($current, ['sales.index','sales.create','sales.edit','sales.show']) ? 'bg-yellow-400 text-gray-900' : 'hover:bg-gray-700' }}">
            Penjualan
        </a>
        <a href="{{ route('categories.index') }}"
           class="px-3 py-1.5 rounded text-sm font-medium transition
                  {{ str_starts_with($current, 'categories') ? 'bg-yellow-400 text-gray-900' : 'hover:bg-gray-700' }}">
            Kategori
        </a>
        <a href="{{ route('sales.trash') }}"
           class="px-3 py-1.5 rounded text-sm font-medium transition
                  {{ $current === 'sales.trash' ? 'bg-yellow-400 text-gray-900' : 'hover:bg-gray-700' }}">
            🗑 Riwayat Hapus
        </a>
    </div>
</nav>
<main class="max-w-6xl mx-auto p-4">
    @if(session('ok'))
        <div class="bg-green-100 border border-green-300 text-green-800 p-3 rounded mb-4">
            ✓ {{ session('ok') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 border border-red-300 text-red-800 p-3 rounded mb-4">
            <div class="font-semibold mb-1">Terdapat kesalahan:</div>
            @foreach($errors->all() as $e)
                <div class="text-sm">• {{ $e }}</div>
            @endforeach
        </div>
    @endif
    @yield('content')
</main>
</body>
</html>