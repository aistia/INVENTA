<?php

use App\Http\Controllers\Admin\AkunAdminController;
use App\Http\Controllers\Admin\BarangController as AdminBarangController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LaporanKerusakanController as AdminLaporanKerusakanController;
use App\Http\Controllers\Admin\PeminjamanController as AdminPeminjamanController;
use App\Http\Controllers\Admin\ProfilController as AdminProfilController;
use App\Http\Controllers\Admin\VerifikasiAkunController as AdminVerifikasiAkunController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Siswa\BarangController as SiswaBarangController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\LaporanKerusakanController as SiswaLaporanKerusakanController;
use App\Http\Controllers\Siswa\PeminjamanController as SiswaPeminjamanController;
use App\Http\Controllers\Siswa\ProfilController as SiswaProfilController;
use App\Http\Controllers\Siswa\VerifikasiController as SiswaVerifikasiController;
use App\Http\Middleware\EnsureAkunTerverifikasi;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return redirect()->route('login');
});

// Menampilkan foto yang diupload (alat, profil, bukti kerusakan) langsung dari
// storage/app/public, jadi tidak bergantung pada link "public/storage"
// (yang sering bermasalah di Windows/OneDrive). Kalau link itu berfungsi normal,
// route ini tidak akan terpakai.
Route::get('/storage/{path}', function (string $path) {
    abort_if(str_contains($path, '..'), 404);

    $disk = Storage::disk('public');
    abort_unless($disk->exists($path), 404);

    return response()->file($disk->path($path), [
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('path', '.*');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.attempt');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Area Siswa / Guru
Route::middleware(['auth', 'role:user'])
    ->prefix('siswa')
    ->name('siswa.')
    ->group(function () {
        Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');

        Route::get('/cek-stok', [SiswaBarangController::class, 'index'])->name('barang.index');

        Route::get('/ajukan-peminjaman', [SiswaPeminjamanController::class, 'create'])->middleware(EnsureAkunTerverifikasi::class)->name('peminjaman.create');
        Route::post('/ajukan-peminjaman', [SiswaPeminjamanController::class, 'store'])->middleware(EnsureAkunTerverifikasi::class)->name('peminjaman.store');
        Route::get('/riwayat-peminjaman', [SiswaPeminjamanController::class, 'index'])->name('peminjaman.index');
        Route::post('/riwayat-peminjaman/{peminjaman}/kembalikan', [SiswaPeminjamanController::class, 'kembalikan'])->name('peminjaman.kembalikan');

        Route::get('/lapor-kerusakan', [SiswaLaporanKerusakanController::class, 'index'])->name('laporan.index');
        Route::get('/lapor-kerusakan/buat', [SiswaLaporanKerusakanController::class, 'create'])->name('laporan.create');
        Route::post('/lapor-kerusakan', [SiswaLaporanKerusakanController::class, 'store'])->name('laporan.store');

        Route::get('/verifikasi-akun', [SiswaVerifikasiController::class, 'index'])->name('verifikasi.index');
        Route::post('/verifikasi-akun', [SiswaVerifikasiController::class, 'store'])->name('verifikasi.store');
        Route::get('/verifikasi-akun/foto', [SiswaVerifikasiController::class, 'foto'])->name('verifikasi.foto');

        Route::get('/profil', [SiswaProfilController::class, 'edit'])->name('profil.edit');
        Route::put('/profil', [SiswaProfilController::class, 'update'])->name('profil.update');
        Route::put('/profil/password', [SiswaProfilController::class, 'updatePassword'])->name('profil.password');
    });

// Area Admin
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/export', [AdminDashboardController::class, 'exportRingkasan'])->name('dashboard.export');

        Route::get('/verifikasi-peminjaman', [AdminPeminjamanController::class, 'index'])->name('peminjaman.index');
        Route::get('/peminjaman/arsip/unduh', [AdminPeminjamanController::class, 'exportArsip'])->name('peminjaman.export-arsip');
        Route::post('/peminjaman/setujui-cepat', [AdminPeminjamanController::class, 'bulkApprove'])->name('peminjaman.bulk-approve');
        Route::post('/peminjaman/{peminjaman}/setujui', [AdminPeminjamanController::class, 'approve'])->name('peminjaman.approve');
        Route::post('/peminjaman/{peminjaman}/tolak', [AdminPeminjamanController::class, 'reject'])->name('peminjaman.reject');
        Route::post('/peminjaman/{peminjaman}/kembali', [AdminPeminjamanController::class, 'returned'])->name('peminjaman.returned');
        Route::post('/peminjaman/{peminjaman}/arsip', [AdminPeminjamanController::class, 'toggleArsip'])->name('peminjaman.arsip');

        Route::get('/kelola-stok', [AdminBarangController::class, 'index'])->name('barang.index');
        Route::get('/kelola-stok/arsip/unduh', [AdminBarangController::class, 'exportArsip'])->name('barang.export-arsip');
        Route::get('/kelola-stok/tambah', [AdminBarangController::class, 'create'])->name('barang.create');
        Route::post('/kelola-stok', [AdminBarangController::class, 'store'])->name('barang.store');
        Route::get('/kelola-stok/{alat}/ubah', [AdminBarangController::class, 'edit'])->name('barang.edit');
        Route::put('/kelola-stok/{alat}', [AdminBarangController::class, 'update'])->name('barang.update');
        Route::delete('/kelola-stok/{alat}', [AdminBarangController::class, 'destroy'])->name('barang.destroy');
        Route::post('/kelola-stok/{alat}/stok', [AdminBarangController::class, 'adjustStock'])->name('barang.stok');
        Route::post('/kelola-stok/{alat}/arsip', [AdminBarangController::class, 'toggleArsip'])->name('barang.arsip');

        Route::get('/tindak-lanjut-kerusakan', [AdminLaporanKerusakanController::class, 'index'])->name('laporan.index');
        Route::get('/tindak-lanjut-kerusakan/arsip/unduh', [AdminLaporanKerusakanController::class, 'exportArsip'])->name('laporan.export-arsip');
        Route::post('/tindak-lanjut-kerusakan/{laporan}/catatan', [AdminLaporanKerusakanController::class, 'updateCatatan'])->name('laporan.catatan');
        Route::post('/tindak-lanjut-kerusakan/{laporan}/tukar', [AdminLaporanKerusakanController::class, 'tukarBarang'])->name('laporan.tukar');
        Route::post('/tindak-lanjut-kerusakan/{laporan}/selesai', [AdminLaporanKerusakanController::class, 'complete'])->name('laporan.selesai');
        Route::post('/tindak-lanjut-kerusakan/{laporan}/arsip', [AdminLaporanKerusakanController::class, 'toggleArsip'])->name('laporan.arsip');

        Route::get('/verifikasi-akun', [AdminVerifikasiAkunController::class, 'index'])->name('verifikasi-akun.index');
        Route::post('/verifikasi-akun/{user}/setujui', [AdminVerifikasiAkunController::class, 'approve'])->name('verifikasi-akun.approve');
        Route::post('/verifikasi-akun/{user}/tolak', [AdminVerifikasiAkunController::class, 'reject'])->name('verifikasi-akun.reject');
        Route::get('/verifikasi-akun/{user}/identitas', [AdminVerifikasiAkunController::class, 'identitas'])->name('verifikasi-akun.identitas');

        Route::get('/kelola-admin', [AkunAdminController::class, 'index'])->name('akun.index');
        Route::post('/kelola-admin', [AkunAdminController::class, 'store'])->name('akun.store');

        Route::get('/profil', [AdminProfilController::class, 'edit'])->name('profil.edit');
        Route::put('/profil', [AdminProfilController::class, 'update'])->name('profil.update');
        Route::put('/profil/password', [AdminProfilController::class, 'updatePassword'])->name('profil.password');
    });