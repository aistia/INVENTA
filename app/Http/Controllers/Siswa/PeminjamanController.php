<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\DetailPeminjaman;
use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PeminjamanController extends Controller
{
    /**
     * Menampilkan form pengajuan peminjaman.
     */
    public function create(Request $request): View
    {
        $alatList = Alat::with('kategori')
            ->where('arsip', false)
            ->where('jumlah_stok', '>', 0)
            ->orderBy('nama_alat')
            ->get();

        $alatTerpilih = $request->filled('alat')
            ? Alat::find($request->integer('alat'))
            : null;

        return view(
            'siswa.peminjaman.create',
            compact('alatList', 'alatTerpilih')
        );
    }

    /**
     * Menyimpan pengajuan peminjaman siswa.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'id_alat' => [
                'required',
                'exists:alat,id_alat',
            ],
            'jumlah' => [
                'required',
                'integer',
                'min:1',
            ],
            'tanggal_pinjam' => [
                'required',
                'date',
                'after_or_equal:today',
            ],
            'tanggal_kembali_rencana' => [
                'required',
                'date',
                'after_or_equal:tanggal_pinjam',
            ],
            'deskripsi' => [
                'required',
                'string',
                'max:1000',
            ],
        ], [], [
            'id_alat' => 'Alat',
            'jumlah' => 'Jumlah',
            'tanggal_pinjam' => 'Tanggal pinjam',
            'tanggal_kembali_rencana' => 'Tanggal kembali',
            'deskripsi' => 'Keperluan',
        ]);

        try {
            DB::transaction(function () use ($data) {

                // Ambil data alat dan kunci baris selama transaksi.
                $alat = Alat::where('id_alat', $data['id_alat'])
                    ->lockForUpdate()
                    ->firstOrFail();

                // Pastikan alat masih tersedia.
                if ($alat->arsip || (int) $alat->jumlah_stok < 1) {
                    throw new \RuntimeException(
                        'Alat ini sudah tidak tersedia untuk dipinjam.'
                    );
                }

                // Pastikan jumlah tidak melebihi stok.
                if ((int) $data['jumlah'] > (int) $alat->jumlah_stok) {
                    throw new \RuntimeException(
                        'Jumlah melebihi stok yang tersedia ('
                        .$alat->jumlah_stok.' unit).'
                    );
                }

                // Buat data utama peminjaman.
                $peminjaman = Peminjaman::create([
                    'user_id' => Auth::id(),
                    'deskripsi' => $data['deskripsi'],
                    'tanggal_pinjam' => $data['tanggal_pinjam'],
                    'tanggal_kembali_rencana' => $data['tanggal_kembali_rencana'],
                    'status' => 'menunggu',
                ]);

                // Simpan jumlah sesuai input siswa.
                DetailPeminjaman::create([
                    'id_peminjaman' => $peminjaman->id_peminjaman,
                    'id_alat' => $data['id_alat'],
                    'jumlah' => (int) $data['jumlah'],
                ]);
            });

        } catch (\RuntimeException $e) {
            return back()
                ->withErrors([
                    'jumlah' => $e->getMessage(),
                ])
                ->withInput();
        }

        return redirect()
            ->route('siswa.peminjaman.index')
            ->with(
                'success',
                'Pengajuan peminjaman berhasil dikirim, menunggu verifikasi petugas lab.'
            );
    }

    /**
     * Siswa mengajukan pengembalian dengan foto bukti.
     *
     * Status dan stok tidak berubah di sini.
     * Admin yang memeriksa kondisi fisik dan mengonfirmasi pengembalian.
     */
    public function kembalikan(
        Request $request,
        Peminjaman $peminjaman
    ): RedirectResponse {
        abort_unless(
            $peminjaman->user_id === Auth::id(),
            403
        );

        if ($peminjaman->status !== 'disetujui') {
            return back()->with(
                'error',
                'Hanya peminjaman yang sedang berjalan yang bisa diajukan pengembaliannya.'
            );
        }

        if ($peminjaman->diajukan_kembali_pada) {
            return back()->with(
                'error',
                'Pengembalian untuk peminjaman ini sudah diajukan. Tunggu konfirmasi petugas lab.'
            );
        }

        $data = $request->validate([
            'foto_pengembalian' => [
                'required',
                'image',
                'max:4096',
            ],
            'catatan_pengembalian' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ], [
            'foto_pengembalian.required' =>
                'Foto bukti kondisi alat wajib diunggah.',

            'foto_pengembalian.image' =>
                'File harus berupa gambar (JPG atau PNG).',

            'foto_pengembalian.max' =>
                'Ukuran foto maksimal 4 MB.',

            'foto_pengembalian.uploaded' =>
                'Foto gagal diunggah, kemungkinan ukurannya terlalu besar. Coba foto lain yang lebih kecil.',
        ], [
            'foto_pengembalian' => 'Foto bukti',
            'catatan_pengembalian' => 'Catatan',
        ]);

        $path = $request
            ->file('foto_pengembalian')
            ->store('pengembalian', 'public');

        $peminjaman->update([
            'foto_pengembalian' => $path,
            'catatan_pengembalian' =>
                $data['catatan_pengembalian'] ?? null,
            'diajukan_kembali_pada' => now(),
        ]);

        return redirect()
            ->route('siswa.peminjaman.index', [
                'tab' => 'disetujui',
            ])
            ->with(
                'success',
                'Pengajuan pengembalian terkirim. Serahkan alat ke petugas lab untuk dicek dan dikonfirmasi.'
            );
    }

    /**
     * Menampilkan riwayat peminjaman siswa.
     */
    public function index(Request $request): View
    {
        $tab = $request->input('tab', 'semua');

        $statusMap = [
            'menunggu' => ['menunggu'],
            'disetujui' => ['disetujui'],
            'ditolak' => ['ditolak'],
            'dikembalikan' => ['dikembalikan'],
        ];

        $peminjaman = Peminjaman::with([
            'detail.alat',
            'admin',
        ])
            ->where('user_id', Auth::id())
            ->when(
                isset($statusMap[$tab]),
                fn ($q) => $q->whereIn('status', $statusMap[$tab])
            )
            ->latest()
            ->paginate(8)
            ->withQueryString();

        $stats = [
            'semua' => Peminjaman::where(
                'user_id',
                Auth::id()
            )->count(),

            'menunggu' => Peminjaman::where(
                'user_id',
                Auth::id()
            )->where('status', 'menunggu')->count(),

            'disetujui' => Peminjaman::where(
                'user_id',
                Auth::id()
            )->where('status', 'disetujui')->count(),

            'ditolak' => Peminjaman::where(
                'user_id',
                Auth::id()
            )->where('status', 'ditolak')->count(),

            'dikembalikan' => Peminjaman::where(
                'user_id',
                Auth::id()
            )->where('status', 'dikembalikan')->count(),
        ];

        return view(
            'siswa.peminjaman.index',
            compact('peminjaman', 'tab', 'stats')
        );
    }
}
