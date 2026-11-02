<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VerifikasiAkunController extends Controller
{
    /** Hanya akun siswa/guru (role "user"), admin tidak ikut diverifikasi di sini. */
    protected function pendaftar()
    {
        return User::whereHas('role', fn ($q) => $q->where('nama_role', 'user'));
    }

    public function index(Request $request): View
    {
        $tab = $request->input('tab', 'menunggu');

        $stats = [
            'menunggu' => $this->pendaftar()->where('status_akun', 'menunggu')->count(),
            'terverifikasi' => $this->pendaftar()->where('status_akun', 'terverifikasi')->count(),
            'ditolak' => $this->pendaftar()->where('status_akun', 'ditolak')->count(),
            'semua' => $this->pendaftar()->count(),
        ];

        $akun = $this->pendaftar()
            ->with('verifikator')
            ->when(in_array($tab, ['menunggu', 'terverifikasi', 'ditolak'], true), fn ($q) => $q->where('status_akun', $tab))
            ->when($request->filled('cari'), function ($q) use ($request) {
                $cari = $request->cari;
                $q->where(function ($sub) use ($cari) {
                    $sub->where('nama', 'like', "%$cari%")
                        ->orWhere('username', 'like', "%$cari%")
                        ->orWhere('email', 'like', "%$cari%")
                        ->orWhere('nis', 'like', "%$cari%")
                        ->orWhere('nip', 'like', "%$cari%");
                });
            })
            ->orderByRaw("FIELD(status_akun, 'menunggu', 'ditolak', 'terverifikasi')")
            // Di dalam tiap status, akun yang datanya sudah lengkap (foto profil + No. HP) tampil lebih dulu
            ->orderByRaw("(foto IS NOT NULL AND foto <> '' AND no_hp IS NOT NULL AND no_hp <> '') DESC")
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.verifikasi-akun.index', compact('akun', 'stats', 'tab'));
    }

    public function approve(User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 404);

        // Data verifikasi harus lengkap: foto profil + No. HP
        $kurang = [];
        if (! filled($user->foto)) {
            $kurang[] = 'foto profil';
        }
        if (! filled($user->no_hp)) {
            $kurang[] = 'No. HP';
        }

        if ($kurang !== []) {
            return back()->with('error', $user->nama.' belum bisa diverifikasi, datanya belum lengkap: '.implode(' dan ', $kurang).'.');
        }

        $user->update([
            'status_akun' => 'terverifikasi',
            'catatan_verifikasi' => null,
            'diverifikasi_oleh' => Auth::id(),
            'diverifikasi_pada' => now(),
        ]);

        return back()->with('success', 'Akun '.$user->nama.' berhasil diverifikasi. Sekarang bisa meminjam alat.');
    }

    /** Dipakai untuk menolak pendaftar baru maupun mencabut verifikasi akun yang sudah aktif. */
    public function reject(Request $request, User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 404);

        $data = $request->validate([
            'alasan' => ['required', 'string', 'max:500'],
        ], [], ['alasan' => 'Alasan']);

        $user->update([
            'status_akun' => 'ditolak',
            'catatan_verifikasi' => $data['alasan'],
            'diverifikasi_oleh' => Auth::id(),
            'diverifikasi_pada' => now(),
        ]);

        return back()->with('success', 'Akun '.$user->nama.' ditandai tidak terverifikasi. Akun ini tidak bisa meminjam alat.');
    }

    /** Foto identitas disimpan privat, hanya bisa dibuka lewat route ini (khusus admin). */
    public function identitas(User $user): StreamedResponse
    {
        abort_if($user->isAdmin(), 404);

        $disk = Storage::disk('local');
        abort_unless($user->foto_identitas && $disk->exists($user->foto_identitas), 404);

        return $disk->response($user->foto_identitas);
    }
}