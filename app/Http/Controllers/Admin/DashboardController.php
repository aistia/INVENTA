<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\DetailPeminjaman;
use App\Models\KategoriAlat;
use App\Models\LaporanKerusakan;
use App\Models\Peminjaman;
use App\Models\StokLog;
use App\Support\ExcelBuilder;
use Illuminate\View\View;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    /**
     * Ekspor ringkasan dashboard ke Excel.
     */
    public function exportRingkasan(): StreamedResponse
    {
        // Statistik dashboard.
        $totalTersedia = (int) Alat::sum('jumlah_stok');

        $totalDipinjamAktif = (int) DetailPeminjaman::whereHas(
            'peminjaman',
            fn ($q) => $q->where('status', 'disetujui')
        )->sum('jumlah');

        $stokSiap = (int) Alat::where('kondisi', 'baik')
            ->sum('jumlah_stok');

        $laporanBaru = LaporanKerusakan::where(
            'status',
            'diproses'
        )->count();

        // Ambil 20 transaksi peminjaman terbaru.
        $peminjamanTerbaru = Peminjaman::with([
            'user',
            'detail.alat',
        ])
            ->latest()
            ->take(20)
            ->get();

        // Ambil 20 laporan kerusakan terbaru.
        $laporanTerbaru = LaporanKerusakan::with([
            'user',
            'alat',
        ])
            ->latest()
            ->take(20)
            ->get();

        // Format tanggal.
        $formatTanggal = fn ($tanggal) => $tanggal
            ? Carbon::parse($tanggal)->translatedFormat('d M Y')
            : '-';

        // Setiap jenis alat dibuat menjadi satu baris.
        $peminjamanRows = $peminjamanTerbaru->flatMap(
            function ($p) use ($formatTanggal) {
                return $p->detail->map(fn ($d) => [
                    'REQ-' . str_pad(
                        (string) $p->id_peminjaman,
                        4,
                        '0',
                        STR_PAD_LEFT
                    ),

                    $p->user->nama ?? '-',

                    $d->alat->nama_alat ?? '-',

                    (int) $d->jumlah,

                    $formatTanggal($p->tanggal_pinjam),

                    $formatTanggal($p->tanggal_kembali_rencana),

                    $formatTanggal($p->tanggal_kembali_aktual),

                    ucfirst($p->status),

                ]);
            }
        )->values()->all();

        // Data laporan kerusakan.
        $laporanRows = $laporanTerbaru->map(
            fn ($lap) => [
                $lap->kodeLaporanLabel(),
                $lap->alat->nama_alat ?? '-',
                $lap->user->nama ?? '-',
                ucfirst($lap->status),
                $formatTanggal($lap->created_at),
            ]
        )->all();

        $waktuUnduh = 'Diunduh pada: '
            . now()->translatedFormat('d F Y H:i');

        // Susun file Excel.
        $builder = (new ExcelBuilder())
            ->addSummarySheet(
                'Ringkasan',
                'Ringkasan Dashboard INVENTA',
                [
                    'Total Stok Tersedia' => $totalTersedia,
                    'Sedang Dipinjam (Unit)' => $totalDipinjamAktif,
                    'Stok Kondisi Baik' => $stokSiap,
                    'Laporan Kerusakan Diproses' => $laporanBaru,
                ]
            )
            ->addSheet(
                'Peminjaman Terbaru',
                [
                    'ID Peminjaman',
                    'Peminjam',
                    'Nama Alat',
                    'Jumlah',
                    'Tanggal Pinjam',
                    'Rencana Kembali',
                    'Dikembalikan Pada',
                    'Status',
                ],
                $peminjamanRows,
                $waktuUnduh
            )
            ->addSheet(
                'Laporan Kerusakan',
                [
                    'Kode Laporan',
                    'Nama Alat',
                    'Pelapor',
                    'Status',
                    'Tanggal Lapor',
                ],
                $laporanRows,
                $waktuUnduh
            );

        return $builder->download(
            'ringkasan-dashboard-' . now()->format('Ymd-His')
        );
    }

    /**
     * Menampilkan dashboard admin.
     */
    public function index(): View
    {
        // --- Statistik kartu atas ---

        $totalTersedia = (int) Alat::sum('jumlah_stok');

        $totalDipinjamAktif = (int) DetailPeminjaman::whereHas(
            'peminjaman',
            fn ($q) => $q->where('status', 'disetujui')
        )->sum('jumlah');

        $totalAset = $totalTersedia + $totalDipinjamAktif;

        $stokSiap = (int) Alat::where('kondisi', 'baik')
            ->sum('jumlah_stok');

        $persenPrima = $totalTersedia > 0
            ? (int) round(
                $stokSiap / $totalTersedia * 100
            )
            : 0;

        $laporanBaru = LaporanKerusakan::where(
            'status',
            'diproses'
        )->count();

        $laporanSegera = LaporanKerusakan::where(
            'status',
            'diproses'
        )
            ->where('created_at', '<=', now()->subDays(2))
            ->count();

        $stats = [
            'total_aset' => $totalAset,
            'sedang_dipinjam' => $totalDipinjamAktif,
            'stok_siap' => $stokSiap,
            'persen_prima' => $persenPrima,
            'laporan_baru' => $laporanBaru,
            'laporan_segera' => $laporanSegera,
        ];

        // --- Ringkasan kondisi aset ---

        $totalKondisiBaik = (int) Alat::whereRaw(
            'LOWER(TRIM(kondisi)) = ?',
            ['baik']
        )
            ->where('arsip', false)
            ->sum('jumlah_stok');

        $totalPerluPerbaikan = (int) Alat::whereRaw(
            'LOWER(TRIM(kondisi)) = ?',
            ['perlu perbaikan']
        )
            ->where('arsip', false)
            ->sum('jumlah_stok');

        // --- Tren sirkulasi 7 hari terakhir ---

        $mulai = now()->subDays(6)->startOfDay();

        $riwayat = Peminjaman::where(
            'created_at',
            '>=',
            $mulai
        )
            ->selectRaw(
                'DATE(created_at) as tanggal, COUNT(*) as total'
            )
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        $labelHari = [];
        $dataTren = [];

        for ($i = 6; $i >= 0; $i--) {
            $tgl = now()->subDays($i);
            $key = $tgl->format('Y-m-d');

            $labelHari[] = ucfirst(
                $tgl->translatedFormat('D')
            );

            $dataTren[] = (int) ($riwayat[$key] ?? 0);
        }

        // --- Distribusi kategori alat ---

        $distribusi = KategoriAlat::query()
            ->withSum('alat as total_stok', 'jumlah_stok')
            ->get()
            ->filter(fn ($k) => $k->total_stok > 0)
            ->sortByDesc('total_stok')
            ->values();

        // --- Peminjaman menunggu persetujuan ---

        $menungguVerifikasi = Peminjaman::with([
            'user',
            'detail.alat',
        ])
            ->where('status', 'menunggu')
            ->latest()
            ->take(3)
            ->get();

        $totalMenunggu = Peminjaman::where(
            'status',
            'menunggu'
        )->count();

        // --- Aktivitas terkini ---

        $logStok = StokLog::with('alat')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($log) => [
                'type' => 'stok',
                'created_at' => $log->created_at,
                'data' => $log,
            ]);

        $logLaporan = LaporanKerusakan::with([
            'alat',
            'user',
        ])
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($lap) => [
                'type' => 'laporan',
                'created_at' => $lap->created_at,
                'data' => $lap,
            ]);

        $aktivitas = $logStok
            ->concat($logLaporan)
            ->sortByDesc('created_at')
            ->take(5)
            ->values();

        // --- Kirim data ke halaman dashboard ---

        return view('admin.dashboard', compact(
            'stats',
            'totalKondisiBaik',
            'totalPerluPerbaikan',
            'labelHari',
            'dataTren',
            'distribusi',
            'menungguVerifikasi',
            'totalMenunggu',
            'aktivitas',
        ));
    }
}
