<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Office;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AbsenController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $office = Office::first();

        $hariIni = Attendance::where('user_id', $user->id)
            ->whereDate('tanggal', today())
            ->first();

        $riwayat = Attendance::where('user_id', $user->id)
            ->orderByDesc('tanggal')
            ->limit(10)
            ->get();

        return view('absen', compact('user', 'office', 'hariIni', 'riwayat'));
    }

    public function masuk(Request $request)
    {
        $data = $this->validasi($request);
        $user = $request->user();
        $office = Office::first();

        $sudah = Attendance::where('user_id', $user->id)->whereDate('tanggal', today())->exists();
        if ($sudah) {
            return back()->with('error', 'Kamu sudah absen masuk hari ini.');
        }

        $jarak = $this->hitungJarak($data['lat'], $data['lng'], $office->latitude, $office->longitude);
        if ($jarak > $office->radius_meter) {
            return back()->with('error', "Lokasi kamu {$jarak} m dari kantor (maksimal {$office->radius_meter} m).");
        }

        $sekarang = now();
        $batas = $sekarang->copy()->setTimeFromTimeString($office->jam_masuk);

        Attendance::create([
            'user_id' => $user->id,
            'tanggal' => $sekarang->toDateString(),
            'masuk_at' => $sekarang,
            'masuk_lat' => $data['lat'],
            'masuk_lng' => $data['lng'],
            'masuk_jarak' => $jarak,
            'masuk_foto' => $this->simpanFoto($data['foto'], $user->id, 'masuk'),
            'status' => $sekarang->gt($batas) ? 'terlambat' : 'tepat_waktu',
        ]);

        return back()->with('success', 'Absen masuk berhasil pukul ' . $sekarang->format('H:i') . '.');
    }

    public function pulang(Request $request)
    {
        $data = $this->validasi($request);
        $user = $request->user();
        $office = Office::first();

        $absen = Attendance::where('user_id', $user->id)->whereDate('tanggal', today())->first();
        if (! $absen || ! $absen->masuk_at) {
            return back()->with('error', 'Kamu belum absen masuk hari ini.');
        }
        if ($absen->pulang_at) {
            return back()->with('error', 'Kamu sudah absen pulang hari ini.');
        }

        $jarak = $this->hitungJarak($data['lat'], $data['lng'], $office->latitude, $office->longitude);
        if ($jarak > $office->radius_meter) {
            return back()->with('error', "Lokasi kamu {$jarak} m dari kantor (maksimal {$office->radius_meter} m).");
        }

        $sekarang = now();

        $absen->update([
            'pulang_at' => $sekarang,
            'pulang_lat' => $data['lat'],
            'pulang_lng' => $data['lng'],
            'pulang_jarak' => $jarak,
            'pulang_foto' => $this->simpanFoto($data['foto'], $user->id, 'pulang'),
        ]);

        return back()->with('success', 'Absen pulang berhasil pukul ' . $sekarang->format('H:i') . '.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'foto' => ['required', 'string'],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);
    }

    private function simpanFoto(string $dataUrl, int $userId, string $jenis): string
    {
        if (! preg_match('/^data:image\/jpeg;base64,/', $dataUrl)) {
            abort(422, 'Format foto tidak valid.');
        }

        $biner = base64_decode(substr($dataUrl, strpos($dataUrl, ',') + 1), true);
        if ($biner === false || strlen($biner) > 2 * 1024 * 1024) {
            abort(422, 'Foto tidak valid atau terlalu besar.');
        }

        $path = 'selfies/' . now()->format('Y-m-d') . '/' . $userId . '_' . $jenis . '_' . now()->format('His') . '.jpg';
        Storage::disk('public')->put($path, $biner);

        return $path;
    }

    private function hitungJarak($lat1, $lng1, $lat2, $lng2): int
    {
        $r = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        return (int) round($r * 2 * atan2(sqrt($a), sqrt(1 - $a)));
    }
}