<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VerifikasiController extends Controller
{
    /**
     * Halaman verifikasi sekarang menyatu dengan Profil Saya.
     * Route ini dipertahankan supaya link lama tidak error.
     */
    public function index(): RedirectResponse
    {
        return redirect()->to(route('siswa.profil.edit').'#verifikasi');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->status_akun === 'terverifikasi') {
            return back()->with('error', 'Akun kamu sudah terverifikasi, tidak perlu mengunggah ulang.');
        }

        $request->validate([
            'foto_identitas' => ['required', 'image', 'max:4096'],
        ], [
            'foto_identitas.required' => 'Foto kartu identitas wajib diunggah.',
            'foto_identitas.image' => 'File harus berupa gambar (JPG atau PNG).',
            'foto_identitas.max' => 'Ukuran foto maksimal 4 MB.',
            'foto_identitas.uploaded' => 'Foto gagal diunggah, kemungkinan ukurannya terlalu besar. Coba foto lain yang lebih kecil.',
        ]);

        $disk = Storage::disk('local');

        if ($user->foto_identitas) {
            $disk->delete($user->foto_identitas);
        }

        $path = $request->file('foto_identitas')->store('identitas', 'local');

        $user->update([
            'foto_identitas' => $path,
            'status_akun' => 'menunggu',
            'catatan_verifikasi' => null,
            'diverifikasi_oleh' => null,
            'diverifikasi_pada' => null,
        ]);

        return redirect()
            ->to(route('siswa.profil.edit').'#verifikasi')
            ->with('success', 'Foto identitas terkirim. Tunggu petugas lab memverifikasi akun kamu.');
    }

    /** Menampilkan foto identitas milik sendiri (privat, tidak bisa diakses orang lain). */
    public function foto(): StreamedResponse
    {
        $user = Auth::user();
        $disk = Storage::disk('local');

        abort_unless($user->foto_identitas && $disk->exists($user->foto_identitas), 404);

        return $disk->response($user->foto_identitas);
    }
}