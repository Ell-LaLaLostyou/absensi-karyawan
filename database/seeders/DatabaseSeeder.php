<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Office;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $office = Office::create([
            'nama' => 'Kantor Pusat',
            'latitude' => -6.2000000,
            'longitude' => 106.8166660,
            'radius_meter' => 100,
            'jam_masuk' => '08:00:00',
            'jam_pulang' => '17:00:00',
        ]);

        $password = Hash::make('password');

        User::forceCreate([
            'name' => 'Admin HRD',
            'email' => 'admin@demo.test',
            'nip' => 'ADM001',
            'role' => 'admin',
            'jabatan' => 'HRD Manager',
            'divisi' => 'HRD',
            'password' => $password,
            'email_verified_at' => now(),
        ]);

        $faker = fake('id_ID');
        $divisi = ['Kreatif', 'Social Media', 'Produksi', 'Finance', 'Account Executive'];
        $jabatan = ['Staff', 'Senior Staff', 'Supervisor'];

        $karyawan = [];
        for ($i = 1; $i <= 25; $i++) {
            $karyawan[] = User::forceCreate([
                'name' => $faker->name(),
                'email' => 'karyawan' . $i . '@demo.test',
                'nip' => 'KRY' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'role' => 'karyawan',
                'jabatan' => $faker->randomElement($jabatan),
                'divisi' => $faker->randomElement($divisi),
                'password' => $password,
                'email_verified_at' => now(),
            ]);
        }

        // Riwayat absen 30 hari terakhir (hari ini sengaja dikosongin buat demo live)
        $start = Carbon::today()->subDays(30);

        foreach ($karyawan as $user) {
            for ($d = $start->copy(); $d->lt(Carbon::today()); $d->addDay()) {
                if ($d->isWeekend()) continue;
                if (random_int(1, 100) <= 8) continue; // ~8% gak masuk

                if (random_int(1, 100) <= 75) {
                    $masuk = $d->copy()->setTime(7, 35)->addMinutes(random_int(0, 24));
                    $status = 'tepat_waktu';
                } else {
                    $masuk = $d->copy()->setTime(8, 0)->addMinutes(random_int(1, 30));
                    $status = 'terlambat';
                }
                $pulang = $d->copy()->setTime(17, 0)->addMinutes(random_int(0, 40));

                Attendance::create([
                    'user_id' => $user->id,
                    'tanggal' => $d->toDateString(),
                    'masuk_at' => $masuk,
                    'masuk_lat' => (float) $office->latitude + random_int(-40, 40) / 100000,
                    'masuk_lng' => (float) $office->longitude + random_int(-40, 40) / 100000,
                    'masuk_jarak' => random_int(5, 70),
                    'pulang_at' => $pulang,
                    'pulang_lat' => (float) $office->latitude + random_int(-40, 40) / 100000,
                    'pulang_lng' => (float) $office->longitude + random_int(-40, 40) / 100000,
                    'pulang_jarak' => random_int(5, 70),
                    'status' => $status,
                ]);
            }
        }
    }
}