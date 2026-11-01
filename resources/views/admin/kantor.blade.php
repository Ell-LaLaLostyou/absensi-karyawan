@extends('layouts.admin')

@section('content')
<h1 class="text-lg font-semibold">Pengaturan Kantor</h1>

<form method="POST" action="{{ route('admin.kantor.simpan') }}" class="bg-white rounded-xl shadow p-4 max-w-lg space-y-3 text-sm">
    @csrf

    <div>
        <label class="block text-xs text-gray-500 mb-1">Nama kantor</label>
        <input type="text" name="nama" value="{{ old('nama', $office->nama) }}" required class="w-full border rounded-lg px-3 py-2">
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Latitude</label>
            <input type="text" name="latitude" id="latitude" value="{{ old('latitude', $office->latitude) }}" required class="w-full border rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Longitude</label>
            <input type="text" name="longitude" id="longitude" value="{{ old('longitude', $office->longitude) }}" required class="w-full border rounded-lg px-3 py-2">
        </div>
    </div>

    <button type="button" id="btnLokasi" class="border rounded-lg px-3 py-1.5">Pakai lokasi saya sekarang</button>
    <span id="infoLokasi" class="text-xs text-gray-500"></span>

    <div>
        <label class="block text-xs text-gray-500 mb-1">Radius absen (meter)</label>
        <input type="number" name="radius_meter" value="{{ old('radius_meter', $office->radius_meter) }}" required class="w-full border rounded-lg px-3 py-2">
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Jam masuk</label>
            <input type="time" name="jam_masuk" value="{{ old('jam_masuk', substr($office->jam_masuk, 0, 5)) }}" required class="w-full border rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Jam pulang</label>
            <input type="time" name="jam_pulang" value="{{ old('jam_pulang', substr($office->jam_pulang, 0, 5)) }}" required class="w-full border rounded-lg px-3 py-2">
        </div>
    </div>

    <button type="submit" class="bg-blue-600 text-white rounded-lg px-4 py-2">Simpan</button>
</form>

<script>
    document.getElementById('btnLokasi').addEventListener('click', function () {
        var info = document.getElementById('infoLokasi');
        info.textContent = 'Mengambil lokasi...';
        navigator.geolocation.getCurrentPosition(function (pos) {
            document.getElementById('latitude').value = pos.coords.latitude.toFixed(7);
            document.getElementById('longitude').value = pos.coords.longitude.toFixed(7);
            info.textContent = 'Terisi, jangan lupa klik Simpan.';
        }, function () {
            info.textContent = 'Lokasi gagal diambil. Izinkan akses lokasi di browser.';
        }, { enableHighAccuracy: true, timeout: 15000 });
    });
</script>
@endsection