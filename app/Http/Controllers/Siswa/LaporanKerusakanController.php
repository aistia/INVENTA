<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\LaporanKerusakan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LaporanKerusakanController extends Controller
{
    /**
     * Menampilkan form laporan kerusakan.
     * Hanya alat dari peminjaman aktif milik siswa yang ditampilkan.
     */
    public function create(Request $request): View
    {
        $alatList = Alat::with('kategori')
            ->where('arsip', false)
            ->whereHas('detailPeminjaman.peminjaman', function ($query) {
                $query
                    ->where('user_id', Auth::id())
                    ->where('status', 'disetujui')
                    ->whereNull('tanggal_kembali_aktual')
                    ->whereNull('diajukan_kembali_pada');
            })
            ->orderBy('nama_alat')
            ->get();

        $alatTerpilih = $request->filled('alat')
            ? $alatList->firstWhere(
                'id_alat',
                $request->integer('alat')
            )
            : null;

        $laporanTerbaru = LaporanKerusakan::with('alat')
            ->where('user_id', Auth::id())
            ->latest()
            ->take(5)
            ->get();

        return view('siswa.laporan.create', compact(
            'alatList',
            'alatTerpilih',
            'laporanTerbaru'
        ));
    }

    /**
     * Menyimpan laporan kerusakan.
     * Memastikan alat benar-benar sedang dipinjam oleh siswa.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'alat_id' => [
                'required',
                'exists:alat,id_alat',
            ],
            'deskripsi' => [
                'required',
                'string',
                'max:2000',
            ],
            'foto_bukti' => [
                'nullable',
                'image',
                'max:4096',
            ],
        ], [], [
            'alat_id' => 'Alat',
            'deskripsi' => 'Deskripsi kerusakan',
            'foto_bukti' => 'Foto bukti',
        ]);

        // Validasi ulang agar siswa tidak bisa mengirim
        // laporan untuk alat yang bukan sedang dipinjamnya.
        $alatValid = Alat::where('id_alat', $data['alat_id'])
            ->where('arsip', false)
            ->whereHas('detailPeminjaman.peminjaman', function ($query) {
                $query
                    ->where('user_id', Auth::id())
                    ->where('status', 'disetujui')
                    ->whereNull('tanggal_kembali_aktual')
                    ->whereNull('diajukan_kembali_pada');
            })
            ->exists();

        if (! $alatValid) {
            return back()
                ->withErrors([
                    'alat_id' => 'Alat tersebut tidak sedang kamu pinjam.',
                ])
                ->withInput();
        }

        // Upload foto bukti jika tersedia.
        $path = null;

        if ($request->hasFile('foto_bukti')) {
            $path = $request->file('foto_bukti')
                ->store('laporan-kerusakan', 'public');
        }

        // Simpan laporan.
        LaporanKerusakan::create([
            'user_id' => Auth::id(),
            'alat_id' => $data['alat_id'],
            'deskripsi' => $data['deskripsi'],
            'foto_bukti' => $path,
            'status' => 'diproses',
        ]);

        return redirect()
            ->route('siswa.laporan.index')
            ->with(
                'success',
                'Laporan kerusakan berhasil dikirim ke petugas lab.'
            );
    }

    /**
     * Menampilkan riwayat laporan kerusakan siswa.
     */
    public function index(Request $request): View
    {
        $tab = $request->input('tab', 'semua');

        $laporan = LaporanKerusakan::with(['alat', 'admin'])
            ->where('user_id', Auth::id())
            ->when(
                $tab === 'diproses',
                fn ($q) => $q->where('status', 'diproses')
            )
            ->when(
                $tab === 'selesai',
                fn ($q) => $q->where('status', 'selesai')
            )
            ->latest()
            ->paginate(8)
            ->withQueryString();

        $stats = [
            'semua' => LaporanKerusakan::where('user_id', Auth::id())
                ->count(),

            'diproses' => LaporanKerusakan::where('user_id', Auth::id())
                ->where('status', 'diproses')
                ->count(),

            'selesai' => LaporanKerusakan::where('user_id', Auth::id())
                ->where('status', 'selesai')
                ->count(),
        ];

        return view('siswa.laporan.index', compact(
            'laporan',
            'tab',
            'stats'
        ));
    }
}
