@extends('layouts.admin')

@section('content')
<h1 class="text-lg font-semibold">Dashboard, {{ now()->translatedFormat('l, d F Y') }}</h1>

<div class="grid grid-cols-2 md:grid-cols-4 gap-3">
    <div class="bg-white rounded-xl shadow p-4">
        <p class="text-xs text-gray-500">Total karyawan</p>
        <p class="text-2xl font-semibold">{{ $totalKaryawan }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4">
        <p class="text-xs text-gray-500">Sudah absen</p>
        <p class="text-2xl font-semibold text-green-600">{{ $hadir }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4">
        <p class="text-xs text-gray-500">Terlambat</p>
        <p class="text-2xl font-semibold text-red-600">{{ $terlambat }}</p>
    </div>
    <div class="bg-white rounded-xl shadow p-4">
        <p class="text-xs text-gray-500">Belum absen</p>
        <p class="text-2xl font-semibold text-gray-600">{{ $belum }}</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-4 overflow-x-auto">
    <p class="font-semibold text-sm mb-2">Absen masuk hari ini</p>
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-gray-500">
                <th class="py-1 pr-3">Nama</th>
                <th class="pr-3">Divisi</th>
                <th class="pr-3">Masuk</th>
                <th class="pr-3">Pulang</th>
                <th class="pr-3">Jarak</th>
                <th class="pr-3">Status</th>
                <th>Foto</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($hariIni as $a)
                <tr class="border-t">
                    <td class="py-1 pr-3">{{ $a->user->name }}</td>
                    <td class="pr-3">{{ $a->user->divisi }}</td>
                    <td class="pr-3">{{ $a->masuk_at ? $a->masuk_at->format('H:i') : '-' }}</td>
                    <td class="pr-3">{{ $a->pulang_at ? $a->pulang_at->format('H:i') : '-' }}</td>
                    <td class="pr-3">{{ $a->masuk_jarak !== null ? $a->masuk_jarak . ' m' : '-' }}</td>
                    <td class="pr-3 {{ $a->status === 'terlambat' ? 'text-red-600' : 'text-green-600' }}">
                        {{ $a->status === 'terlambat' ? 'Terlambat' : 'Tepat waktu' }}
                    </td>
                    <td>
                        @if ($a->masuk_foto)
                            <a href="{{ asset('storage/' . $a->masuk_foto) }}" target="_blank">
                                <img src="{{ asset('storage/' . $a->masuk_foto) }}" class="w-10 h-10 rounded object-cover" alt="selfie">
                            </a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="py-3 text-gray-500">Belum ada yang absen hari ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection