<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ticketing 2 Stroke Meet Up</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
<nav class="bg-gray-900 text-white">
    <div class="max-w-5xl mx-auto px-4 py-3 flex gap-6 items-center">
        <span class="font-bold">2 Stroke Meet Up</span>
        <a href="{{ route('recap') }}" class="hover:underline">Rekap</a>
        <a href="{{ route('sales.index') }}" class="hover:underline">Penjualan</a>
        <a href="{{ route('categories.index') }}" class="hover:underline">Kategori</a>
    </div>
</nav>
<main class="max-w-5xl mx-auto p-4">
    @if(session('ok'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('ok') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif
    @yield('content')
</main>
</body>
</html>