<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [], [
            'identifier' => 'Username / Email',
            'password' => 'Kata sandi',
        ]);

        // Login bisa memakai email ATAU username, sesuai kolom yang cocok.
        $field = filter_var($data['identifier'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($field, $data['identifier'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return back()
                ->withErrors(['identifier' => 'Username/Email atau kata sandi yang Anda masukkan salah.'])
                ->onlyInput('identifier');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return $this->redirectByRole();
    }

    public function showRegisterForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectByRole();
        }

        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        // Registrasi mandiri hanya untuk siswa/guru (role "user").
        // Akun admin/petugas lab dibuat lewat seeder atau oleh admin lain.
        // Username boleh diketik dengan atau tanpa "@" di depan (mengikuti tampilan form).
        $request->merge(['username' => ltrim((string) $request->input('username'), '@')]);

        $data = $request->validate([
            'tipe_user' => ['required', 'in:siswa,guru'],
            'nama' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:user,username'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:user,email'],
            'nis' => ['required_if:tipe_user,siswa', 'nullable', 'string', 'max:30'],
            'nip' => ['required_if:tipe_user,guru', 'nullable', 'string', 'max:30'],
            'kelas' => ['required_if:tipe_user,siswa', 'nullable', 'string', 'max:30'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'terms' => ['accepted'],
        ], [], [
            'tipe_user' => 'Tipe pengguna',
            'nama' => 'Nama lengkap',
            'username' => 'Username',
            'email' => 'Email',
            'nis' => 'NIS',
            'nip' => 'NIP',
            'kelas' => 'Kelas',
            'no_hp' => 'No. HP',
            'password' => 'Kata sandi',
            'terms' => 'Persetujuan SOP & tata tertib',
        ]);

        $roleUser = Role::firstOrCreate(['nama_role' => 'user']);

        $user = User::create([
            'id_role' => $roleUser->id_role,
            'username' => $data['username'],
            'nama' => $data['nama'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'tipe_user' => $data['tipe_user'],
            'nis' => $data['tipe_user'] === 'siswa' ? $data['nis'] : null,
            'nip' => $data['tipe_user'] === 'guru' ? $data['nip'] : null,
            'kelas' => $data['tipe_user'] === 'siswa' ? $data['kelas'] : null,
            'no_hp' => $data['no_hp'] ?? null,
        ]);

        Auth::login($user);

        return redirect()->route('siswa.dashboard')->with('success', 'Akun berhasil dibuat. Selamat datang di INVENTA!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    protected function redirectByRole(): RedirectResponse
    {
        return Auth::user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('siswa.dashboard');
    }
}
