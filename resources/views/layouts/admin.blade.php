<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Absensi Karyawan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <nav class="bg-white shadow">
        <div class="max-w-6xl mx-auto px-4 py-3 flex items-center gap-4 text-sm">
            <span class="font-semibold">Absensi Admin</span>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'font-semibold text-blue-600' : '' }}">Dashboard</a>
            <a href="{{ route('admin.rekap') }}" class="{{ request()->routeIs('admin.rekap') ? 'font-semibold text-blue-600' : '' }}">Rekap</a>
            <a href="{{ route('admin.kantor') }}" class="{{ request()->routeIs('admin.kantor') ? 'font-semibold text-blue-600' : '' }}">Kantor</a>
            <a href="{{ route('absen') }}" class="text-gray-500">Halaman absen</a>
            <form method="POST" action="{{ route('logout') }}" class="ml-auto">
                @csrf
                <button type="submit" class="text-red-600">Keluar</button>
            </form>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto p-4 space-y-4">
        @if (session('success'))
            <div class="bg-green-100 text-green-800 rounded-lg p-3 text-sm">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-800 rounded-lg p-3 text-sm">{{ $errors->first() }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>