<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menahan akun siswa/guru yang belum diverifikasi admin agar tidak bisa
 * mengajukan peminjaman. Admin selalu lolos.
 */
class EnsureAkunTerverifikasi
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->isTerverifikasi()) {
            return redirect()
                ->route('siswa.verifikasi.index')
                ->with('error', 'Akun kamu belum diverifikasi petugas lab, jadi belum bisa meminjam alat. Lengkapi verifikasi akun dulu.');
        }

        return $next($request);
    }
}