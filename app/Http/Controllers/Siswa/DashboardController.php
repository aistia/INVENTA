<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\LaporanKerusakan;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();

        $stats = [
            'aktif' => Peminjaman::where('user_id', $userId)->where('status', 'disetujui')->count(),
            'jatuh_tempo_hari_ini' => Peminjaman::where('user_id', $userId)
                ->where('status', 'disetujui')
                ->whereDate('tanggal_kembali_rencana', today())
                ->count(),
            'menunggu' => Peminjaman::where('user_id', $userId)->where('status', 'menunggu')->count(),
            'total_riwayat' => Peminjaman::where('user_id', $userId)->count(),
            'laporan' => LaporanKerusakan::where('user_id', $userId)->count(),
        ];

        $totalSelesai = Peminjaman::where('user_id', $userId)->where('status', 'dikembalikan')->count();
        $selesaiTepatWaktu = Peminjaman::where('user_id', $userId)
            ->where('status', 'dikembalikan')
            ->whereColumn('tanggal_kembali_aktual', '<=', 'tanggal_kembali_rencana')
            ->count();
        $stats['persen_tepat_waktu'] = $totalSelesai > 0 ? (int) round($selesaiTepatWaktu / $totalSelesai * 100) : 100;

        $peminjamanAktif = Peminjaman::with('detail.alat')
            ->where('user_id', $userId)
            ->where('status', 'disetujui')
            ->orderBy('tanggal_kembali_rencana')
            ->get();

        $laporanTerbaru = LaporanKerusakan::with('alat')
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        return view('siswa.dashboard', compact('stats', 'peminjamanAktif', 'laporanTerbaru'));
    }
}