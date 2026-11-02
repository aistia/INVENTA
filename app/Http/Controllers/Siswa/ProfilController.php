<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\LaporanKerusakan;
use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function edit(): View
    {
        // verifikator dibutuhkan kartu status verifikasi (nama petugas yang memverifikasi)
        $user = Auth::user()->load('verifikator');

        $stats = [
            'total_peminjaman' => Peminjaman::where('user_id', $user->id_user)->count(),
            'total_laporan' => LaporanKerusakan::where('user_id', $user->id_user)->count(),
            'bergabung_sejak' => $user->created_at,
        ];

        return view('siswa.profil.edit', compact('user', 'stats'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'kelas' => [$user->tipe_user === 'siswa' ? 'required' : 'nullable', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:150', 'unique:user,email,'.$user->id_user.',id_user'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'foto' => ['nullable', 'image', 'max:5120'],
        ], [
            'foto.image' => 'Foto profil harus berupa gambar (JPG atau PNG).',
            'foto.max' => 'Ukuran foto profil maksimal 5 MB.',
            'foto.uploaded' => 'Foto gagal diunggah, kemungkinan ukurannya terlalu besar. Coba foto lain yang lebih kecil.',
        ], [
            'nama' => 'Nama lengkap',
            'kelas' => 'Kelas',
            'email' => 'Email',
            'no_hp' => 'No. HP',
        ]);

        $fotoBaru = false;

        if ($request->hasFile('foto')) {
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            $data['foto'] = $request->file('foto')->store('profil', 'public');
            $fotoBaru = true;
        }

        $user->update($data);

        $pesan = 'Profil berhasil diperbarui.';

        // Alur verifikasi: data dianggap lengkap kalau No. HP dan foto profil sudah ada
        if ($user->status_akun !== 'terverifikasi') {
            $lengkap = filled($user->foto) && filled($user->no_hp);

            if (! $lengkap) {
                $kurang = [];
                if (! filled($user->no_hp)) {
                    $kurang[] = 'No. HP';
                }
                if (! filled($user->foto)) {
                    $kurang[] = 'foto profil';
                }
                $pesan = 'Profil disimpan. Data verifikasi belum lengkap: '.implode(' dan ', $kurang).'.';
            } elseif ($user->status_akun === 'ditolak') {
                // Setelah ditolak: kalau datanya diperbaiki, kirim ulang ke antrean verifikasi
                $dataBerubah = $fotoBaru || $user->wasChanged(['nama', 'kelas', 'email', 'no_hp']);

                if ($dataBerubah) {
                    $user->update([
                        'status_akun' => 'menunggu',
                        'catatan_verifikasi' => null,
                        'diverifikasi_oleh' => null,
                        'diverifikasi_pada' => null,
                    ]);
                    $pesan = 'Data diperbarui dan dikirim ulang. Tunggu petugas lab memverifikasi akun kamu.';
                }
            } else {
                $pesan = 'Data lengkap dan terkirim. Tunggu petugas lab memverifikasi akun kamu.';
            }
        }

        return redirect()
            ->to(route('siswa.profil.edit').'#verifikasi')
            ->with('success', $pesan);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [], [
            'current_password' => 'Password saat ini',
            'password' => 'Password baru',
        ]);

        if (! Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini yang kamu masukkan salah.'])->withInput();
        }

        $user->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', 'Password berhasil diganti. Gunakan password baru saat login berikutnya.');
    }
}