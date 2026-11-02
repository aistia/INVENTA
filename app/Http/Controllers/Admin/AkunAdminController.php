<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaporanKerusakan;
use App\Models\Peminjaman;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AkunAdminController extends Controller
{
    public function index(): View
    {
        $admins = User::whereHas('role', fn ($q) => $q->where('nama_role', 'admin'))
            ->orderBy('created_at')
            ->get();

        // Jumlah pekerjaan yang pernah ditangani tiap admin (berdasarkan kolom admin_id)
        $totalVerifikasi = Peminjaman::selectRaw('admin_id, COUNT(*) as total')
            ->whereNotNull('admin_id')
            ->groupBy('admin_id')
            ->pluck('total', 'admin_id');

        $totalPerbaikan = LaporanKerusakan::selectRaw('admin_id, COUNT(*) as total')
            ->whereNotNull('admin_id')
            ->groupBy('admin_id')
            ->pluck('total', 'admin_id');

        return view('admin.akun.index', compact('admins', 'totalVerifikasi', 'totalPerbaikan'));
    }

    public function store(Request $request): RedirectResponse
    {
        // Username boleh diketik dengan atau tanpa "@" di depan.
        $request->merge(['username' => ltrim((string) $request->input('username'), '@')]);

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9._-]+$/', 'unique:user,username'],
            'email' => ['required', 'email', 'max:150', 'unique:user,email'],
            'jabatan' => ['nullable', 'string', 'max:100'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
        ], [
            'username.regex' => 'Username hanya boleh berisi huruf, angka, titik, garis bawah, dan strip.',
        ], [
            'nama' => 'Nama lengkap',
            'username' => 'Username',
            'email' => 'Email',
            'jabatan' => 'Jabatan',
            'no_hp' => 'No. WhatsApp',
            'password' => 'Password',
        ]);

        $roleAdmin = Role::firstOrCreate(['nama_role' => 'admin']);

        User::create([
            'id_role' => $roleAdmin->id_role,
            'username' => $data['username'],
            'nama' => $data['nama'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'tipe_user' => null,
            'jabatan' => $data['jabatan'] ?? null,
            'no_hp' => $data['no_hp'] ?? null,
            'status_akun' => 'terverifikasi',
            'diverifikasi_oleh' => auth()->id(),
            'diverifikasi_pada' => now(),
        ]);

        return redirect()
            ->route('admin.akun.index')
            ->with('success', 'Akun admin "'.$data['nama'].'" berhasil dibuat. Beri tahu username & password-nya agar bisa login.');
    }
}