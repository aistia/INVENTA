<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\LaporanKerusakan;
use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PeminjamanController extends Controller
{
    public function index(Request $request): View
    {
        $stats = [
            'menunggu' => Peminjaman::where('status', 'menunggu')->count(),
            'disetujui' => Peminjaman::where('status', 'disetujui')->count(),
            'ditolak' => Peminjaman::where('status', 'ditolak')->count(),
            'dikembalikan' => Peminjaman::where('status', 'dikembalikan')->count(),
            'arsip' => Peminjaman::where('arsip', true)->count(),
        ];

        $tab = $request->input('tab', 'semua');

        $statusMap = [
            'menunggu' => ['menunggu'],
            'disetujui' => ['disetujui'],
            'ditolak' => ['ditolak'],
            'dikembalikan' => ['dikembalikan'],
        ];

        $peminjaman = Peminjaman::with([
            'user',
            'admin',
            'detail.alat',
        ])
            ->when(
                $tab === 'arsip',
                fn ($q) => $q->where('arsip', true),
                fn ($q) => $q->where('arsip', false)
            )
            ->when(
                isset($statusMap[$tab]),
                fn ($q) => $q->whereIn('status', $statusMap[$tab])
            )
            ->when($request->filled('cari'), function ($q) use ($request) {
                $cari = $request->cari;

                $q->where(function ($sub) use ($cari) {
                    $sub->whereHas('user', fn ($u) => $u
                        ->where('nama', 'like', "%$cari%")
                        ->orWhere('nis', 'like', "%$cari%")
                        ->orWhere('nip', 'like', "%$cari%"))
                        ->orWhereHas('detail.alat', fn ($a) => $a
                            ->where('nama_alat', 'like', "%$cari%"));
                });
            })
            ->latest()
            ->paginate(8)
            ->withQueryString();

        $totalSemua = Peminjaman::where('arsip', false)->count();

        return view(
            'admin.peminjaman.index',
            compact('peminjaman', 'stats', 'tab', 'totalSemua')
        );
    }

    public function bulkApprove(Request $request): RedirectResponse
    {
        $ids = (array) $request->input('ids', []);

        $daftar = Peminjaman::with(['detail', 'user'])
            ->whereIn('id_peminjaman', $ids)
            ->where('status', 'menunggu')
            ->get();

        $disetujui = 0;
        $dilewati = 0;

        foreach ($daftar as $p) {
            if (! $p->user || ! $p->user->isTerverifikasi()) {
                $dilewati++;
                continue;
            }

            $p->update([
                'admin_id' => Auth::id(),
                'status' => 'disetujui',
            ]);

            $disetujui++;
        }

        if ($disetujui === 0) {
            return back()->with(
                'error',
                $dilewati > 0
                    ? 'Tidak ada yang disetujui: akun peminjamnya belum terverifikasi.'
                    : 'Tidak ada pengajuan terpilih yang bisa disetujui.'
            );
        }

        $pesan = $disetujui.' pengajuan berhasil disetujui sekaligus.';

        if ($dilewati > 0) {
            $pesan .= ' '.$dilewati
                .' pengajuan dilewati karena akun peminjamnya belum terverifikasi.';
        }

        return back()->with('success', $pesan);
    }

    public function toggleArsip(Peminjaman $peminjaman): RedirectResponse
    {
        if ($peminjaman->status === 'menunggu' && ! $peminjaman->arsip) {
            return back()->with(
                'error',
                'Pengajuan yang masih menunggu verifikasi tidak bisa diarsipkan.'
            );
        }

        $peminjaman->update([
            'arsip' => ! $peminjaman->arsip,
        ]);

        $pesan = $peminjaman->arsip
            ? 'diarsipkan'
            : 'dikembalikan dari arsip';

        return back()->with(
            'success',
            'Peminjaman '.$peminjaman->id_peminjaman
                .' berhasil '.$pesan.'.'
        );
    }

    public function exportArsip(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $data = Peminjaman::with(['user', 'admin', 'detail.alat'])
            ->where('arsip', true)
            ->latest()
            ->get();

        $rows = $data->map(fn ($p) => [
            'REQ-'.str_pad((string) $p->id_peminjaman, 4, '0', STR_PAD_LEFT),
            $p->user->nama ?? '-',
            $p->user->nis ?? $p->user->nip ?? '-',
            $p->detail->map(
                fn ($d) => ($d->alat->nama_alat ?? '-').' x'.$d->jumlah
            )->implode(', '),
            ucfirst($p->status),
            $p->deskripsi,
            $p->tanggal_pinjam?->translatedFormat('d M Y') ?? '-',
            $p->tanggal_kembali_rencana?->translatedFormat('d M Y') ?? '-',
            $p->tanggal_kembali_aktual?->translatedFormat('d M Y') ?? '-',
            $p->catatan_pengembalian ?: '-',
            $p->foto_pengembalian
                ? asset('storage/'.$p->foto_pengembalian)
                : '-',
            $p->admin->nama ?? '-',
            $p->created_at->translatedFormat('d M Y H:i'),
        ])->all();

        return (new \App\Support\ExcelBuilder())
            ->addSheet(
                'Arsip Peminjaman',
                [
                    'ID Peminjaman',
                    'Peminjam',
                    'NIS/NIP',
                    'Alat & Jumlah',
                    'Status',
                    'Keperluan',
                    'Tanggal Pinjam',
                    'Rencana Kembali',
                    'Kembali Aktual',
                    'Catatan Pengembalian',
                    'Link Foto Bukti Pengembalian Siswa',
                    'Penanggung Jawab',
                    'Diajukan Pada',
                ],
                $rows,
                'Diunduh pada: '.now()->translatedFormat('d F Y H:i')
            )
            ->download('arsip-peminjaman-'.now()->format('Ymd'));
    }

    public function approve(Peminjaman $peminjaman): RedirectResponse
    {
        if ($peminjaman->status !== 'menunggu') {
            return back()->with(
                'error',
                'Pengajuan ini sudah diproses sebelumnya.'
            );
        }

        if (! $peminjaman->user || ! $peminjaman->user->isTerverifikasi()) {
            return back()->with(
                'error',
                'Akun peminjam belum terverifikasi, pengajuan tidak bisa disetujui. Verifikasi akunnya dulu di menu Verifikasi Akun.'
            );
        }

        $peminjaman->update([
            'admin_id' => Auth::id(),
            'status' => 'disetujui',
        ]);

        return back()->with(
            'success',
            'Peminjaman '.$peminjaman->id_peminjaman.' disetujui.'
        );
    }

    /**
     * Admin mengonfirmasi pengembalian barang.
     */
    public function returned(
        Request $request,
        Peminjaman $peminjaman
    ): RedirectResponse {
        if ($peminjaman->status !== 'disetujui') {
            return back()->with(
                'error',
                'Hanya peminjaman yang sedang berjalan yang bisa ditandai kembali.'
            );
        }

        if (empty($peminjaman->foto_pengembalian)) {
            return back()->with(
                'error',
                'Pengembalian tidak dapat dikonfirmasi karena siswa belum mengunggah foto bukti pengembalian.'
            );
        }

        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.baik' => ['required', 'integer', 'min:0'],
            'items.*.servis' => ['required', 'integer', 'min:0'],
            'items.*.rusak' => ['required', 'integer', 'min:0'],
        ]);

        $peminjaman->load('detail');

        foreach ($peminjaman->detail as $detail) {
            $item = $validated['items'][$detail->id_detail] ?? null;

            if (! $item) {
                return back()->withInput()->with(
                    'error',
                    'Kondisi setiap barang wajib diisi.'
                );
            }

            $total = (int) $item['baik']
                + (int) $item['servis']
                + (int) $item['rusak'];

            if ($total !== (int) $detail->jumlah) {
                return back()->withInput()->with(
                    'error',
                    'Jumlah baik, servis, dan rusak harus sama dengan jumlah yang dipinjam.'
                );
            }
        }

        $totalBaik = 0;
        $totalServis = 0;
        $totalRusak = 0;

        DB::transaction(function () use (
            $peminjaman,
            $validated,
            &$totalBaik,
            &$totalServis,
            &$totalRusak
        ) {
            foreach ($peminjaman->detail as $detail) {
                $item = $validated['items'][$detail->id_detail];

                $baik = (int) $item['baik'];
                $servis = (int) $item['servis'];
                $rusak = (int) $item['rusak'];

                $detail->update([
                    'jumlah_baik_kembali' => $baik,
                    'jumlah_servis_kembali' => $servis,
                    'jumlah_rusak_kembali' => $rusak,
                ]);

                if ($servis > 0) {
                    Alat::where('id_alat', $detail->id_alat)
                        ->increment('jumlah_servis', $servis);
                }

                if ($rusak > 0) {
                    Alat::where('id_alat', $detail->id_alat)
                        ->increment('jumlah_rusak', $rusak);
                }

                $kodeReq = 'REQ-'.str_pad(
                    (string) $peminjaman->id_peminjaman,
                    4,
                    '0',
                    STR_PAD_LEFT
                );

                // Laporan unit servis.
                if ($servis > 0) {
                    LaporanKerusakan::create([
                        'user_id' => $peminjaman->user_id,
                        'admin_id' => Auth::id(),
                        'alat_id' => $detail->id_alat,
                        'jenis_penanganan' => 'servis',
                        'jumlah_unit' => $servis,
                        'deskripsi' => 'Ditemukan '.$servis
                            .' unit perlu servis saat pengembalian '.$kodeReq.'.'
                            .($peminjaman->catatan_pengembalian
                                ? ' Catatan siswa: '.$peminjaman->catatan_pengembalian
                                : ''),
                        'foto_bukti' => $peminjaman->foto_pengembalian,
                        'status' => 'diproses',
                    ]);
                }

                // Laporan unit rusak.
                if ($rusak > 0) {
                    LaporanKerusakan::create([
                        'user_id' => $peminjaman->user_id,
                        'admin_id' => Auth::id(),
                        'alat_id' => $detail->id_alat,
                        'jenis_penanganan' => 'rusak',
                        'jumlah_unit' => $rusak,
                        'deskripsi' => 'Ditemukan '.$rusak
                            .' unit rusak saat pengembalian '.$kodeReq.'.'
                            .($peminjaman->catatan_pengembalian
                                ? ' Catatan siswa: '.$peminjaman->catatan_pengembalian
                                : ''),
                        'foto_bukti' => $peminjaman->foto_pengembalian,
                        'status' => 'diproses',
                    ]);
                }

                $totalBaik += $baik;
                $totalServis += $servis;
                $totalRusak += $rusak;
            }

            $peminjaman->update([
                'admin_id' => Auth::id(),
                'status' => 'dikembalikan',
                'tanggal_kembali_aktual' => today(),
            ]);
        });

        $pesan = 'Pengembalian dicatat: '.$totalBaik
            .' unit baik kembali ke stok tersedia';

        if ($totalServis > 0 || $totalRusak > 0) {
            $pesan .= ', '.$totalServis
                .' perlu servis, '.$totalRusak
                .' rusak (tidak masuk stok tersedia)';
        }

        return back()->with('success', $pesan.'.');
    }

    public function reject(
        Request $request,
        Peminjaman $peminjaman
    ): RedirectResponse {
        if ($peminjaman->status !== 'menunggu') {
            return back()->with(
                'error',
                'Pengajuan ini sudah diproses sebelumnya.'
            );
        }

        $peminjaman->update([
            'admin_id' => Auth::id(),
            'status' => 'ditolak',
            'deskripsi' => trim(
                ($peminjaman->deskripsi ? $peminjaman->deskripsi.' | ' : '')
                .'Ditolak: '.$request->input('alasan', '-')
            ),
        ]);

        return back()->with(
            'success',
            'Peminjaman '.$peminjaman->id_peminjaman.' ditolak.'
        );
    }
}
