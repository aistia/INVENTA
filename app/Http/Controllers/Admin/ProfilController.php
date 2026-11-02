<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\StokLog;
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
        $user = Auth::user();

        $stats = [
            'alat_ditangani' => StokLog::where('admin_id', $user->id_user)->count(),
            'verifikasi_bulan_ini' => Peminjaman::where('admin_id', $user->id_user)
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year)
                ->count(),
            'bergabung_sejak' => $user->created_at,
        ];

        return view('admin.profil.edit', compact('user', 'stats'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'jabatan' => ['nullable', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:user,email,'.$user->id_user.',id_user'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'foto' => ['nullable', 'image', 'max:5120'],
        ], [
            'foto.image' => 'Foto profil harus berupa gambar (JPG atau PNG).',
            'foto.max' => 'Ukuran foto profil maksimal 5 MB.',
            'foto.uploaded' => 'Foto gagal diunggah, kemungkinan ukurannya terlalu besar. Coba foto lain yang lebih kecil.',
        ], [
            'nama' => 'Nama lengkap',
            'jabatan' => 'Jabatan',
            'email' => 'Email',
            'no_hp' => 'No. WhatsApp',
        ]);

        if ($request->hasFile('foto')) {
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }
            $data['foto'] = $request->file('foto')->store('profil', 'public');
        }

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
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