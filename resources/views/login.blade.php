<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - Absensi Karyawan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        laut: '#12304A',
                        laut2: '#1B4466',
                        kertas: '#F6F8F9',
                        stempel: '#F2B632',
                        tinta: '#0F2233',
                        abu: '#5B6B78'
                    },
                    fontFamily: {
                        display: ['"Bricolage Grotesque"', 'system-ui', 'sans-serif'],
                        body: ['"DM Sans"', 'system-ui', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        .angka { font-variant-numeric: tabular-nums; }
        @keyframes kedip { 50% { opacity: 0.25; } }
        .titik { animation: kedip 1s steps(1) infinite; }
        @media (prefers-reduced-motion: reduce) { .titik { animation: none; } }
    </style>
</head>
<body class="font-body bg-kertas text-tinta antialiased">
<main class="min-h-screen lg:grid lg:grid-cols-[1.1fr_1fr]">

    <section class="bg-laut text-white px-6 py-8 lg:px-14 lg:py-14 flex flex-col justify-between">
        <p class="font-display font-semibold text-lg flex items-center">
            <span class="inline-block w-3 h-3 bg-stempel rounded-sm mr-2"></span>
            Absensi Karyawan
        </p>

        <div class="my-10 lg:my-0">
            <p id="tanggal" class="text-white/70 text-sm lg:text-base"></p>
            <p class="font-display font-extrabold angka leading-none tracking-tight text-[4.5rem] sm:text-[6rem] lg:text-[9rem]">
                <span id="jam">00</span><span class="titik text-stempel">:</span><span id="menit">00</span>
            </p>
        </div>

        <div class="hidden lg:block">
            <p class="font-display font-semibold text-2xl max-w-md">Satu kali absen mencatat tiga hal.</p>
            <ul class="mt-5 grid grid-cols-3 gap-4 max-w-xl text-sm">
                <li>
                    <svg class="w-6 h-6 text-stempel" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7"/></svg>
                    <p class="mt-2 font-semibold">Wajah</p>
                    <p class="text-white/70">Selfie langsung dari kamera.</p>
                </li>
                <li>
                    <svg class="w-6 h-6 text-stempel" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>
                    <p class="mt-2 font-semibold">Lokasi</p>
                    <p class="text-white/70">Koordinat GPS dan jarak ke kantor.</p>
                </li>
                <li>
                    <svg class="w-6 h-6 text-stempel" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                    <p class="mt-2 font-semibold">Waktu</p>
                    <p class="text-white/70">Jam dicatat server, bukan HP.</p>
                </li>
            </ul>
        </div>
    </section>

    <section class="flex items-center px-6 py-10 lg:px-16">
        <div class="w-full max-w-sm mx-auto lg:mx-0">
            <h1 class="font-display font-extrabold text-3xl">Masuk</h1>
            <p class="mt-1 text-abu">Pakai email dan password akun kamu.</p>

            @error('email')
                <div role="alert" class="mt-5 rounded-lg border border-red-200 bg-red-50 text-red-700 px-3 py-2 text-sm">{{ $message }}</div>
            @enderror

            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium mb-1">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 focus:outline-none focus:border-laut focus:ring-2 focus:ring-laut/25">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium mb-1">Password</label>
                    <div class="relative">
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 pr-16 focus:outline-none focus:border-laut focus:ring-2 focus:ring-laut/25">
                        <button type="button" id="lihat" class="absolute inset-y-0 right-0 px-3 text-sm text-abu hover:text-tinta">Lihat</button>
                    </div>
                </div>
                <button type="submit"
                        class="w-full rounded-lg bg-laut hover:bg-laut2 text-white font-semibold py-3 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-laut">
                    Masuk
                </button>
            </form>

            <div class="mt-8 border-t border-slate-200 pt-5">
                <p class="text-sm text-abu">Coba dengan akun demo. Klik untuk mengisi email otomatis.</p>
                <div class="mt-2 flex gap-2">
                    <button type="button" data-email="karyawan1@demo.test"
                            class="demo rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm hover:border-laut">Karyawan</button>
                    <button type="button" data-email="admin@demo.test"
                            class="demo rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-sm hover:border-laut">Admin</button>
                </div>
                <p class="mt-2 text-xs text-abu">Password semua akun demo: password</p>
            </div>
        </div>
    </section>
</main>

<script>
    (function () {
        var hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        var bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        function dua(n) { return String(n).padStart(2, '0'); }

        function tick() {
            var d = new Date();
            document.getElementById('jam').textContent = dua(d.getHours());
            document.getElementById('menit').textContent = dua(d.getMinutes());
            document.getElementById('tanggal').textContent =
                hari[d.getDay()] + ', ' + d.getDate() + ' ' + bulan[d.getMonth()] + ' ' + d.getFullYear();
        }
        tick();
        setInterval(tick, 1000);

        var pw = document.getElementById('password');
        var lihat = document.getElementById('lihat');
        lihat.addEventListener('click', function () {
            var tampil = pw.type === 'password';
            pw.type = tampil ? 'text' : 'password';
            lihat.textContent = tampil ? 'Sembunyi' : 'Lihat';
        });

        document.querySelectorAll('.demo').forEach(function (b) {
            b.addEventListener('click', function () {
                document.getElementById('email').value = b.dataset.email;
                pw.value = 'password';
                document.getElementById('email').focus();
            });
        });
    })();
</script>
</body>
</html>