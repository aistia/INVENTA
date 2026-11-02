<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\LaporanKerusakan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LaporanKerusakanController extends Controller
{
    private const TUKAR_MARKER = '[TUKAR_BARANG]';

    private const HAPUS_MARKER = '[TINDAKAN: HAPUS BARANG]';

    private const GANTI_MARKER = '[TINDAKAN: GANTI BARANG BARU]';

    /**
     * Menampilkan daftar laporan kerusakan.
     */
    public function index(Request $request): View
    {
        $stats = [
            'diproses' => LaporanKerusakan::where('status', 'diproses')->count(),

            'segera' => LaporanKerusakan::where('status', 'diproses')
                ->where('created_at', '<=', now()->subDays(2))
                ->count(),

            'selesai_bulan_ini' => LaporanKerusakan::where('status', 'selesai')
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year)
                ->count(),

            'total' => LaporanKerusakan::count(),

            'arsip' => LaporanKerusakan::where('arsip', true)->count(),
        ];

        $tab = $request->input('tab', 'semua');

        $topAlatBermasalah = LaporanKerusakan::selectRaw(
            'alat_id, COUNT(*) as total'
        )
            ->with('alat')
            ->groupBy('alat_id')
            ->orderByDesc('total')
            ->take(4)
            ->get();

        $laporan = LaporanKerusakan::with(['user', 'alat', 'admin'])
            ->when(
                $tab === 'arsip',
                fn ($q) => $q->where('arsip', true),
                fn ($q) => $q->where('arsip', false)
            )
            ->when(
                $tab === 'diproses',
                fn ($q) => $q->where('status', 'diproses')
            )
            ->when(
                $tab === 'selesai',
                fn ($q) => $q->where('status', 'selesai')
            )
            ->when($request->filled('cari'), function ($q) use ($request) {
                $cari = $request->cari;

                $q->where(function ($sub) use ($cari) {
                    $sub->whereHas('user', fn ($u) => $u
                        ->where('nama', 'like', "%$cari%"))
                        ->orWhereHas('alat', fn ($a) => $a
                            ->where('nama_alat', 'like', "%$cari%"));
                });
            })
            ->orderByRaw("FIELD(status, 'diproses', 'selesai')")
            ->latest()
            ->paginate(6)
            ->withQueryString();

        return view(
            'admin.laporan.index',
            compact('stats', 'tab', 'laporan', 'topAlatBermasalah')
        );
    }

    /**
     * Menyimpan catatan tanpa menghapus penanda penukaran.
     */
    public function updateCatatan(
        Request $request,
        LaporanKerusakan $laporan
    ): RedirectResponse {
        $data = $request->validate([
            'catatan_tindak_lanjut' => ['required', 'string', 'max:2000'],
        ], [], [
            'catatan_tindak_lanjut' => 'Catatan servis',
        ]);

        $catatan = trim($data['catatan_tindak_lanjut']);

        if ($this->sudahDitukar($laporan)) {
            $catatan = self::TUKAR_MARKER . "\n" . $catatan;
        }

        $laporan->update([
            'admin_id' => Auth::id(),
            'catatan_tindak_lanjut' => $catatan,
        ]);

        return back()->with(
            'success',
            'Catatan servis untuk ' . $laporan->kodeLaporanLabel() . ' berhasil disimpan.'
        );
    }

    /**
     * Menukar barang.
     *
     * Stok barang pengganti berkurang.
     * Unit barang lama masuk ke jumlah_servis.
     */
    public function tukarBarang(
        LaporanKerusakan $laporan
    ): RedirectResponse {
        try {
            $jumlah = DB::transaction(function () use ($laporan) {
                $laporanTerkunci = LaporanKerusakan::where(
                    'id_laporan',
                    $laporan->id_laporan
                )
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($laporanTerkunci->status !== 'diproses') {
                    throw new \RuntimeException(
                        'Hanya laporan yang masih diproses yang bisa ditukar.'
                    );
                }

                if ($this->sudahDitukar($laporanTerkunci)) {
                    throw new \RuntimeException(
                        'Penukaran barang sudah pernah diajukan.'
                    );
                }

                $jumlah = max(1, (int) $laporanTerkunci->jumlah_unit);

                $alat = Alat::where(
                    'id_alat',
                    $laporanTerkunci->alat_id
                )
                    ->lockForUpdate()
                    ->firstOrFail();

                if ((int) $alat->jumlah_stok < $jumlah) {
                    throw new \RuntimeException(
                        'Stok barang pengganti tidak mencukupi.'
                    );
                }

                // Stok barang pengganti berkurang.
                $alat->decrement('jumlah_stok', $jumlah);

                // Barang lama masuk ke antrean servis.
                $alat->increment('jumlah_servis', $jumlah);

                $catatanLama = trim(
                    (string) $laporanTerkunci->catatan_tindak_lanjut
                );

                $catatanBaru = self::TUKAR_MARKER;

                if ($catatanLama !== '') {
                    $catatanBaru .= "\n" . $catatanLama;
                }

                $laporanTerkunci->update([
                    'admin_id' => Auth::id(),
                    'catatan_tindak_lanjut' => $catatanBaru,
                ]);

                return $jumlah;
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with(
            'success',
            $jumlah . ' unit barang pengganti berhasil diberikan. Barang lama masuk ke antrean servis dan stok pengganti telah dikurangi.'
        );
    }

    /**
     * Menyelesaikan laporan kerusakan.
     */
    public function complete(
        Request $request,
        LaporanKerusakan $laporan
    ): RedirectResponse {
        $rules = [
            'catatan_tindak_lanjut' => ['required', 'string', 'max:2000'],
        ];

        // Pilihan Hapus/Ganti hanya untuk laporan rusak
        // yang belum melalui proses Tukar Barang.
        if (
            $laporan->jenis_penanganan === 'rusak'
            && ! $this->sudahDitukar($laporan)
        ) {
            $rules['tindakan_rusak'] = ['required', 'in:hapus,ganti'];
        }

        $data = $request->validate($rules, [], [
            'catatan_tindak_lanjut' => 'Catatan penyelesaian',
            'tindakan_rusak' => 'Tindakan barang rusak',
        ]);

        try {
            $pesanTindakan = DB::transaction(function () use (
                $laporan,
                $data
            ) {
                $laporanTerkunci = LaporanKerusakan::where(
                    'id_laporan',
                    $laporan->id_laporan
                )
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($laporanTerkunci->status === 'selesai') {
                    throw new \RuntimeException(
                        'Laporan ini sudah diselesaikan sebelumnya.'
                    );
                }

                $jumlah = max(
                    1,
                    (int) $laporanTerkunci->jumlah_unit
                );

                $alat = Alat::where(
                    'id_alat',
                    $laporanTerkunci->alat_id
                )
                    ->lockForUpdate()
                    ->firstOrFail();

                $sudahTukar = $this->sudahDitukar($laporanTerkunci);

                $catatanTambahan = '';
                $pesan = '';

                /*
                 * 1. LAPORAN YANG SUDAH DITUKAR
                 *
                 * Unit lama sebelumnya sudah masuk jumlah_servis.
                 * Jika servis berhasil, pindahkan kembali ke stok tersedia.
                 */
                if ($sudahTukar) {
                    if ((int) $alat->jumlah_servis < $jumlah) {
                        throw new \RuntimeException(
                            'Jumlah unit sedang servis tidak mencukupi. Periksa data stok terlebih dahulu.'
                        );
                    }

                    $alat->decrement('jumlah_servis', $jumlah);
                    $alat->increment('jumlah_stok', $jumlah);

                    $pesan = $jumlah .
                        ' unit hasil perbaikan berhasil dikembalikan ke stok tersedia.';
                }

                /*
                 * 2. PENANGANAN SERVIS TANPA PENUKARAN
                 */
                elseif (
                    $laporanTerkunci->jenis_penanganan === 'servis'
                ) {
                    if ((int) $alat->jumlah_servis < $jumlah) {
                        throw new \RuntimeException(
                            'Jumlah unit servis tidak mencukupi untuk diselesaikan.'
                        );
                    }

                    $alat->decrement('jumlah_servis', $jumlah);
                    $alat->increment('jumlah_stok', $jumlah);

                    $pesan = $jumlah .
                        ' unit hasil servis berhasil dikembalikan ke stok tersedia.';
                }

                /*
                 * 3. BARANG RUSAK TANPA PENUKARAN
                 *
                 * Admin memilih Hapus atau Ganti.
                 */
                elseif (
                    $laporanTerkunci->jenis_penanganan === 'rusak'
                ) {
                    if ((int) $alat->jumlah_rusak < $jumlah) {
                        throw new \RuntimeException(
                            'Jumlah unit rusak tidak mencukupi. Periksa kembali data stok.'
                        );
                    }

                    if ($data['tindakan_rusak'] === 'hapus') {
                        $alat->decrement('jumlah_rusak', $jumlah);

                        $catatanTambahan = self::HAPUS_MARKER;

                        $pesan = $jumlah .
                            ' unit rusak berhasil dihapus dari inventaris.';
                    } else {
                        $alat->decrement('jumlah_rusak', $jumlah);
                        $alat->increment('jumlah_stok', $jumlah);

                        $catatanTambahan = self::GANTI_MARKER;

                        $pesan = $jumlah .
                            ' unit rusak berhasil diganti dan stok tersedia bertambah.';
                    }
                }

                /*
                 * 4. LAPORAN UMUM TANPA JENIS PENANGANAN
                 * Dan belum pernah melalui Tukar Barang.
                 */
                else {
                    $pesan = 'Laporan berhasil diselesaikan.';
                }

                // Menyimpan catatan penyelesaian.
                $catatan = trim(
                    (string) $data['catatan_tindak_lanjut']
                );

                if ($catatanTambahan !== '') {
                    $catatan = $catatanTambahan . "\n" . $catatan;
                }

                // Penanda Tukar Barang harus tetap ada.
                if ($sudahTukar) {
                    $catatan = self::TUKAR_MARKER . "\n" . $catatan;
                }

                $laporanTerkunci->update([
                    'admin_id' => Auth::id(),
                    'status' => 'selesai',
                    'catatan_tindak_lanjut' => $catatan,
                ]);

                return $pesan;
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with(
            'success',
            'Laporan ' . $laporan->kodeLaporanLabel() .
            ' berhasil diselesaikan. ' . $pesanTindakan
        );
    }

    /**
     * Mengarsipkan atau mengembalikan laporan dari arsip.
     */
    public function toggleArsip(
        LaporanKerusakan $laporan
    ): RedirectResponse {
        if (
            $laporan->status !== 'selesai'
            && ! $laporan->arsip
        ) {
            return back()->with(
                'error',
                'Hanya laporan berstatus "Selesai" yang bisa diarsipkan.'
            );
        }

        $laporan->update([
            'arsip' => ! $laporan->arsip,
        ]);

        $pesan = $laporan->arsip
            ? 'diarsipkan'
            : 'dikembalikan dari arsip';

        return back()->with(
            'success',
            'Laporan ' . $laporan->kodeLaporanLabel() .
            ' berhasil ' . $pesan . '.'
        );
    }

    /**
     * Mengunduh data arsip laporan kerusakan.
     */
    public function exportArsip(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $data = LaporanKerusakan::with(['user', 'alat', 'admin'])
            ->where('arsip', true)
            ->latest()
            ->get();

        $rows = $data->map(function ($lap) {
            $catatan = str_replace(
                [
                    self::TUKAR_MARKER,
                    self::HAPUS_MARKER,
                    self::GANTI_MARKER,
                ],
                '',
                (string) $lap->catatan_tindak_lanjut
            );

            return [
                $lap->kodeLaporanLabel(),
                $lap->alat->nama_alat ?? '-',
                $lap->user->nama ?? '-',
                $lap->deskripsi,
                ucfirst($lap->status),
                trim($catatan),
                $lap->admin->nama ?? '-',
                $lap->created_at->translatedFormat('d M Y H:i'),
            ];
        })->all();

        return (new \App\Support\ExcelBuilder())
            ->addSheet('Arsip Laporan Kerusakan', [
                'Kode Laporan',
                'Alat',
                'Pelapor',
                'Deskripsi Kerusakan',
                'Status',
                'Catatan Servis',
                'Ditangani Oleh',
                'Tanggal Lapor',
            ], $rows, 'Diunduh pada: ' . now()->translatedFormat('d F Y H:i'))
            ->download(
                'arsip-laporan-kerusakan-' . now()->format('Ymd')
            );
    }

    /**
     * Memeriksa apakah laporan sudah pernah ditukar.
     */
    private function sudahDitukar(
        LaporanKerusakan $laporan
    ): bool {
        return str_contains(
            (string) $laporan->catatan_tindak_lanjut,
            self::TUKAR_MARKER
        );
    }
}
