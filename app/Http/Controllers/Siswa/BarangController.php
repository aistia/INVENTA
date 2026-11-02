<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\KategoriAlat;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BarangController extends Controller
{
    public function index(Request $request): View
    {
        $alat = Alat::query()
            ->with('kategori')
            ->where('arsip', false)
            ->when($request->filled('cari'), function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('nama_alat', 'like', '%'.$request->cari.'%')
                        ->orWhere('kode_alat', 'like', '%'.$request->cari.'%');
                });
            })
            ->when($request->filled('kategori'), fn ($q) => $q->where('id_kategori', $request->kategori))
            ->when($request->filled('kondisi'), fn ($q) => $q->where('kondisi', $request->kondisi))
            ->orderBy('nama_alat')
            ->paginate(12)
            ->withQueryString();

        $kategoriList = KategoriAlat::orderBy('nama_kategori')->get();

      $stats = [
    
    'total_jenis' => Alat::count(),
    'total_unit' => (int) Alat::where('arsip', false)->sum('jumlah_stok'),
    
    'tersedia' => Alat::where('arsip', false)
        ->where('kondisi', 'baik')
        ->where('jumlah_stok', '>', 0)
        ->count(),
];
        return view('siswa.barang.index', compact('alat', 'kategoriList', 'stats'));
    }
}