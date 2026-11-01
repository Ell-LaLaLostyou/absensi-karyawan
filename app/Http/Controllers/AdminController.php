<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Office;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalKaryawan = User::where('role', 'karyawan')->count();

        $hariIni = Attendance::with('user')
            ->whereDate('tanggal', today())
            ->whereHas('user', fn ($q) => $q->where('role', 'karyawan'))
            ->orderBy('masuk_at')
            ->get();

        $hadir = $hariIni->count();
        $terlambat = $hariIni->where('status', 'terlambat')->count();
        $belum = $totalKaryawan - $hadir;

        return view('admin.dashboard', compact('totalKaryawan', 'hariIni', 'hadir', 'terlambat', 'belum'));
    }

    public function rekap(Request $request)
    {
        [$query, $dari, $sampai] = $this->queryRekap($request);

        $data = $query->paginate(20)->withQueryString();

        $daftarDivisi = User::where('role', 'karyawan')
            ->whereNotNull('divisi')
            ->distinct()
            ->orderBy('divisi')
            ->pluck('divisi');

        return view('admin.rekap', compact('data', 'dari', 'sampai', 'daftarDivisi'));
    }

    public function exportCsv(Request $request)
    {
        [$query] = $this->queryRekap($request);
        $rows = $query->get();
        $nama = 'rekap-absen-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['NIP', 'Nama', 'Divisi', 'Tanggal', 'Masuk', 'Pulang', 'Jarak Masuk (m)', 'Status'], ',', '"', '\\');

            foreach ($rows as $a) {
                fputcsv($out, [
                    $a->user->nip,
                    $a->user->name,
                    $a->user->divisi,
                    $a->tanggal->format('Y-m-d'),
                    $a->masuk_at ? $a->masuk_at->format('H:i') : '',
                    $a->pulang_at ? $a->pulang_at->format('H:i') : '',
                    $a->masuk_jarak,
                    $a->status === 'terlambat' ? 'Terlambat' : 'Tepat waktu',
                ], ',', '"', '\\');
            }

            fclose($out);
        }, $nama, ['Content-Type' => 'text/csv']);
    }

    public function kantor()
    {
        return view('admin.kantor', ['office' => Office::first()]);
    }

    public function simpanKantor(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius_meter' => ['required', 'integer', 'min:10', 'max:5000'],
            'jam_masuk' => ['required', 'date_format:H:i'],
            'jam_pulang' => ['required', 'date_format:H:i'],
        ]);

        Office::first()->update($data);

        return back()->with('success', 'Pengaturan kantor disimpan.');
    }

    private function queryRekap(Request $request): array
    {
        $dari = $request->input('dari', now()->subDays(6)->toDateString());
        $sampai = $request->input('sampai', now()->toDateString());

        $query = Attendance::with('user')
            ->whereDate('tanggal', '>=', $dari)
            ->whereDate('tanggal', '<=', $sampai)
            ->orderByDesc('tanggal')
            ->orderBy('masuk_at');

        if ($request->filled('divisi')) {
            $query->whereHas('user', fn ($q) => $q->where('divisi', $request->input('divisi')));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return [$query, $dari, $sampai];
    }
}