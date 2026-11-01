@extends('layouts.admin')

@section('content')
<h1 class="text-lg font-semibold">Rekap Absensi</h1>

<form method="GET" action="{{ route('admin.rekap') }}" class="bg-white rounded-xl shadow p-4 flex flex-wrap items-end gap-3 text-sm">
    <div>
        <label class="block text-xs text-gray-500 mb-1">Dari</label>
        <input type="date" name="dari" value="{{ $dari }}" class="border rounded-lg px-2 py-1">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Sampai</label>
        <input type="date" name="sampai" value="{{ $sampai }}" class="border rounded-lg px-2 py-1">
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Divisi</label>
        <select name="divisi" class="border rounded-lg px-2 py-1">
            <option value="">Semua</option>
            @foreach ($daftarDivisi as $d)
                <option value="{{ $d }}" @selected(request('divisi') === $d)>{{ $d }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs text-gray-500 mb-1">Status</label>
        <select name="status" class="border rounded-lg px-2 py-1">
            <option value="">Semua</option>
            <option value="tepat_waktu" @selected(request('status') === 'tepat_waktu')>Tepat waktu</option>
            <option value="terlambat" @selected(request('status') === 'terlambat')>Terlambat</option>
        </select>
    </div>
    <button type="submit" class="bg-blue-600 text-white rounded-lg px-4 py-1.5">Filter</button>
    <a href="{{ route('admin.rekap.csv', request()->query()) }}" class="border rounded-lg px-4 py-1.5">Export CSV</a>
</form>

<div class="bg-white rounded-xl shadow p-4 overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="text-left text-gray-500">
                <th class="py-1 pr-3">Tanggal</th>
                <th class="pr-3">Nama</th>
                <th class="pr-3">Divisi</th>
                <th class="pr-3">Masuk</th>
                <th class="pr-3">Pulang</th>
                <th class="pr-3">Jarak</th>
                <th class="pr-3">Status</th>
                <th>Foto</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $a)
                <tr class="border-t">
                    <td class="py-1 pr-3">{{ $a->tanggal->format('d M Y') }}</td>
                    <td class="pr-3">{{ $a->user->name }}<span class="block text-xs text-gray-400">{{ $a->user->nip }}</span></td>
                    <td class="pr-3">{{ $a->user->divisi }}</td>
                    <td class="pr-3">{{ $a->masuk_at ? $a->masuk_at->format('H:i') : '-' }}</td>
                    <td class="pr-3">{{ $a->pulang_at ? $a->pulang_at->format('H:i') : '-' }}</td>
                    <td class="pr-3">{{ $a->masuk_jarak !== null ? $a->masuk_jarak . ' m' : '-' }}</td>
                    <td class="pr-3 {{ $a->status === 'terlambat' ? 'text-red-600' : 'text-green-600' }}">
                        {{ $a->status === 'terlambat' ? 'Terlambat' : 'Tepat waktu' }}
                    </td>
                    <td class="flex gap-1">
                        @if ($a->masuk_foto)
                            <a href="{{ asset('storage/' . $a->masuk_foto) }}" target="_blank">
                                <img src="{{ asset('storage/' . $a->masuk_foto) }}" class="w-10 h-10 rounded object-cover" alt="masuk">
                            </a>
                        @endif
                        @if ($a->pulang_foto)
                            <a href="{{ asset('storage/' . $a->pulang_foto) }}" target="_blank">
                                <img src="{{ asset('storage/' . $a->pulang_foto) }}" class="w-10 h-10 rounded object-cover" alt="pulang">
                            </a>
                        @endif
                        @if (! $a->masuk_foto && ! $a->pulang_foto)
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="py-3 text-gray-500">Tidak ada data untuk filter ini.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div>{{ $data->links() }}</div>
@endsection