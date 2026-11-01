@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto p-4 space-y-4">

    <div class="flex items-center justify-between">
        <div>
            <p class="font-semibold">{{ $user->name }}</p>
            <p class="text-xs text-gray-500">{{ $user->nip }} · {{ $user->divisi }}</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-red-600">Keluar</button>
        </form>
    </div>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 rounded-lg p-3 text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 text-red-800 rounded-lg p-3 text-sm">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="bg-red-100 text-red-800 rounded-lg p-3 text-sm">{{ $errors->first() }}</div>
    @endif

    <div class="bg-white rounded-xl shadow p-4 space-y-3">
        <p class="text-sm text-gray-500">{{ now()->translatedFormat('l, d F Y') }}</p>
        <p class="text-3xl font-semibold" id="jam">{{ now()->format('H:i:s') }}</p>

        @php
            $selesai = $hariIni && $hariIni->masuk_at && $hariIni->pulang_at;
            $sudahMasuk = $hariIni && $hariIni->masuk_at;
        @endphp

        @if ($selesai)
            <p class="text-sm text-green-700">Absen hari ini sudah lengkap. Masuk {{ $hariIni->masuk_at->format('H:i') }}, pulang {{ $hariIni->pulang_at->format('H:i') }}.</p>
        @else
            <div class="relative rounded-lg overflow-hidden bg-black aspect-[3/4]">
                <video id="video" autoplay playsinline muted class="w-full h-full object-cover" style="transform: scaleX(-1);"></video>
            </div>
            <canvas id="canvas" class="hidden"></canvas>

            <p class="text-xs text-gray-600" id="statusLokasi">Mengambil lokasi...</p>
            <p class="text-xs text-gray-400">Batas: {{ $office->radius_meter }} m dari {{ $office->nama }}</p>

            <form id="formAbsen" method="POST" action="{{ $sudahMasuk ? route('absen.pulang') : route('absen.masuk') }}">
                @csrf
                <input type="hidden" name="foto" id="foto">
                <input type="hidden" name="lat" id="lat">
                <input type="hidden" name="lng" id="lng">
                <button type="button" id="btnAbsen" disabled
                        class="w-full bg-blue-600 disabled:bg-gray-300 text-white rounded-lg py-3 font-medium">
                    {{ $sudahMasuk ? 'Absen Pulang' : 'Absen Masuk' }}
                </button>
            </form>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow p-4">
        <p class="font-semibold mb-2 text-sm">Riwayat 10 hari terakhir</p>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500">
                        <th class="py-1 pr-2">Tanggal</th>
                        <th class="pr-2">Masuk</th>
                        <th class="pr-2">Pulang</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayat as $r)
                        <tr class="border-t">
                            <td class="py-1 pr-2">{{ $r->tanggal->format('d M') }}</td>
                            <td class="pr-2">{{ $r->masuk_at ? $r->masuk_at->format('H:i') : '-' }}</td>
                            <td class="pr-2">{{ $r->pulang_at ? $r->pulang_at->format('H:i') : '-' }}</td>
                            <td class="{{ $r->status === 'terlambat' ? 'text-red-600' : 'text-green-600' }}">
                                {{ $r->status === 'terlambat' ? 'Terlambat' : 'Tepat waktu' }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-2 text-gray-500">Belum ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // jam berjalan (tampilan saja, waktu resmi tetap dari server)
    setInterval(function () {
        document.getElementById('jam').textContent = new Date().toLocaleTimeString('id-ID', { hour12: false });
    }, 1000);

    var video = document.getElementById('video');
    var canvas = document.getElementById('canvas');
    var btn = document.getElementById('btnAbsen');
    var statusLokasi = document.getElementById('statusLokasi');

    if (video) {
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false })
            .then(function (stream) { video.srcObject = stream; })
            .catch(function () { alert('Kamera tidak bisa diakses. Izinkan akses kamera di browser.'); });

        navigator.geolocation.getCurrentPosition(function (pos) {
            document.getElementById('lat').value = pos.coords.latitude;
            document.getElementById('lng').value = pos.coords.longitude;
            statusLokasi.textContent = 'Lokasi siap: ' + pos.coords.latitude.toFixed(6) + ', ' + pos.coords.longitude.toFixed(6);
            btn.disabled = false;
        }, function () {
            statusLokasi.textContent = 'Lokasi gagal diambil. Izinkan akses lokasi di browser.';
        }, { enableHighAccuracy: true, timeout: 15000 });

        btn.addEventListener('click', function () {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            var ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            var teks = new Date().toLocaleString('id-ID') + ' | ' +
                Number(document.getElementById('lat').value).toFixed(5) + ', ' +
                Number(document.getElementById('lng').value).toFixed(5);
            ctx.fillStyle = 'rgba(0,0,0,0.55)';
            ctx.fillRect(0, canvas.height - 36, canvas.width, 36);
            ctx.fillStyle = '#ffffff';
            ctx.font = '16px sans-serif';
            ctx.fillText(teks, 10, canvas.height - 12);

            document.getElementById('foto').value = canvas.toDataURL('image/jpeg', 0.8);
            btn.disabled = true;
            document.getElementById('formAbsen').submit();
        });
    }
</script>
@endsection