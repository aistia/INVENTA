<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\KategoriAlat;
use App\Models\StokLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BarangController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->input('tab', 'aktif');

        $alat = Alat::query()
            ->with('kategori')
            ->when($tab === 'aktif', fn ($q) => $q->where('arsip', false))
            ->when($tab === 'arsip', fn ($q) => $q->where('arsip', true))
            ->when($request->filled('cari'), function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('nama_alat', 'like', '%'.$request->cari.'%')
                        ->orWhere('kode_alat', 'like', '%'.$request->cari.'%');
                });
            })
            ->when($request->filled('kategori'), fn ($q) => $q->where('id_kategori', $request->kategori))
            ->orderBy('nama_alat')
            ->paginate(10)
            ->withQueryString();

        $kategoriList = KategoriAlat::orderBy('nama_kategori')->get();

        $stats = [
            'total_perangkat' => (int) Alat::where('arsip', false)->sum('jumlah_stok'),
            'total_kategori' => KategoriAlat::count(),
            'kondisi_baik' => (int) Alat::where('arsip', false)->where('kondisi', 'baik')->sum('jumlah_stok'),
            'perlu_perhatian' => Alat::where('arsip', false)->whereIn('kondisi', ['perlu_servis', 'rusak'])->count(),
            'total_arsip' => Alat::where('arsip', true)->count(),
        ];

        $logTerakhir = StokLog::with(['alat', 'admin'])->latest()->take(6)->get();

        return view('admin.barang.index', compact('alat', 'kategoriList', 'stats', 'logTerakhir', 'tab'));
    }

    public function create(): View
    {
        $kategoriList = KategoriAlat::orderBy('nama_kategori')->get();

        return view('admin.barang.create', compact('kategoriList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['id_kategori' => ['required_without:kategori_baru']], [], ['id_kategori' => 'Kategori']);

        $validated = $this->validateData($request);
        $validated['id_kategori'] = $this->resolveKategori($request);

        $alat = Alat::create([
            'id_kategori' => $validated['id_kategori'],
            'kode_alat' => $validated['kode_alat'] ?: 'AST-'.Str::upper(Str::random(6)),
            'nama_alat' => $validated['nama_alat'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'foto' => $request->hasFile('foto') ? $request->file('foto')->store('alat', 'public') : null,
            'jumlah_stok' => 0,
            'kondisi' => $validated['kondisi'],
        ]);

        // Stok awal dicatat lewat stok_log (jenis masuk) supaya konsisten
        // dengan trigger MySQL & tercatat riwayatnya, bukan ditulis langsung.
        if ((int) $validated['jumlah_stok'] > 0) {
            StokLog::create([
                'id_alat' => $alat->id_alat,
                'admin_id' => Auth::id(),
                'jenis' => 'masuk',
                'jumlah' => (int) $validated['jumlah_stok'],
                'keterangan' => 'Stok awal saat barang didaftarkan',
            ]);
        }

        return redirect()->route('admin.barang.index')->with('success', 'Barang "'.$alat->nama_alat.'" berhasil ditambahkan.');
    }

    public function edit(Alat $alat): View
    {
        $kategoriList = KategoriAlat::orderBy('nama_kategori')->get();

        return view('admin.barang.edit', compact('alat', 'kategoriList'));
    }

    public function update(Request $request, Alat $alat): RedirectResponse
    {
        $request->validate(['id_kategori' => ['required_without:kategori_baru']], [], ['id_kategori' => 'Kategori']);

        $validated = $this->validateData($request, $alat->id_alat);
        $validated['id_kategori'] = $this->resolveKategori($request);

        $fotoPath = $alat->foto;
        if ($request->hasFile('foto')) {
            if ($fotoPath) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($fotoPath);
            }
            $fotoPath = $request->file('foto')->store('alat', 'public');
        }

        $alat->update([
            'id_kategori' => $validated['id_kategori'],
            'kode_alat' => $validated['kode_alat'],
            'nama_alat' => $validated['nama_alat'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'foto' => $fotoPath,
            'kondisi' => $validated['kondisi'],
        ]);

        return redirect()->route('admin.barang.index')->with('success', 'Data "'.$alat->nama_alat.'" berhasil diperbarui.');
    }

    public function destroy(Alat $alat): RedirectResponse
    {
        $nama = $alat->nama_alat;
        if ($alat->foto) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($alat->foto);
        }
        $alat->delete();

        return redirect()->route('admin.barang.index')->with('success', 'Barang "'.$nama.'" berhasil dihapus.');
    }

    /**
     * Arsipkan / batalkan arsip — data tetap ada di tabel, cuma disembunyikan
     * dari daftar aktif dan bisa difilter lewat tab "Arsip".
     */
    public function toggleArsip(Alat $alat): RedirectResponse
    {
        $alat->update(['arsip' => ! $alat->arsip]);

        $pesan = $alat->arsip ? 'diarsipkan' : 'dikembalikan dari arsip';

        return back()->with('success', 'Barang "'.$alat->nama_alat.'" berhasil '.$pesan.'.');
    }

    /**
     * Unduh data alat yang diarsipkan sebagai file Excel (.xlsx) yang rapi.
     */
    public function exportArsip(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $data = Alat::with('kategori')->where('arsip', true)->orderBy('nama_alat')->get();

        $rows = $data->map(fn ($item) => [
            $item->kode_alat,
            $item->nama_alat,
            $item->kategori->nama_kategori ?? '-',
            ucfirst(str_replace('_', ' ', $item->kondisi)),
            $item->jumlah_stok,
            $item->deskripsi,
        ])->all();

        return (new \App\Support\ExcelBuilder())
            ->addSheet(
                'Arsip Barang',
                ['Kode Alat', 'Nama Alat', 'Kategori', 'Kondisi', 'Jumlah Stok', 'Deskripsi'],
                $rows,
                'Diunduh pada: '.now()->translatedFormat('d F Y H:i')
            )
            ->download('arsip-barang-'.now()->format('Ymd'));
    }

    /**
     * Penyesuaian stok manual (barang masuk / keluar di luar alur peminjaman).
     */
    public function adjustStock(Request $request, Alat $alat): RedirectResponse
    {
        $data = $request->validate([
            'jenis' => ['required', 'in:masuk,keluar'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [], ['jumlah' => 'Jumlah', 'jenis' => 'Jenis']);

        if ($data['jenis'] === 'keluar' && $data['jumlah'] > $alat->jumlah_stok) {
            return back()->with('error', 'Jumlah keluar melebihi stok yang tersedia ('.$alat->jumlah_stok.').');
        }

        StokLog::create([
            'id_alat' => $alat->id_alat,
            'admin_id' => Auth::id(),
            'jenis' => $data['jenis'],
            'jumlah' => $data['jumlah'],
            'keterangan' => $data['keterangan'] ?? ($data['jenis'] === 'masuk' ? 'Penambahan stok manual' : 'Pengurangan stok manual'),
        ]);

        return back()->with('success', 'Stok "'.$alat->nama_alat.'" berhasil diperbarui.');
    }

    protected function validateData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'kode_alat' => ['nullable', 'string', 'max:50', 'unique:alat,kode_alat'.($ignoreId ? ",$ignoreId,id_alat" : '')],
            'nama_alat' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'kondisi' => ['required', 'in:baik,perlu_servis,rusak'],
            'jumlah_stok' => ['nullable', 'integer', 'min:0'],
            'foto' => ['nullable', 'image', 'max:2048'],
            'id_kategori' => ['nullable', 'exists:kategori_alat,id_kategori'],
            'kategori_baru' => ['nullable', 'string', 'max:100'],
        ], [], [
            'kode_alat' => 'Kode alat',
            'nama_alat' => 'Nama alat',
            'kondisi' => 'Kondisi',
            'jumlah_stok' => 'Jumlah stok awal',
        ]);
    }

    protected function resolveKategori(Request $request): int
    {
        if ($request->filled('kategori_baru')) {
            return KategoriAlat::firstOrCreate(['nama_kategori' => trim($request->input('kategori_baru'))])->id_kategori;
        }

        return (int) $request->input('id_kategori');
    }
}