<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->enum('status_akun', ['menunggu', 'terverifikasi', 'ditolak'])->default('menunggu')->after('foto');
            $table->string('foto_identitas')->nullable()->after('status_akun');
            $table->text('catatan_verifikasi')->nullable()->after('foto_identitas');
            $table->foreignId('diverifikasi_oleh')->nullable()->after('catatan_verifikasi')
                ->constrained('user', 'id_user')->nullOnDelete();
            $table->timestamp('diverifikasi_pada')->nullable()->after('diverifikasi_oleh');
        });

        // Semua akun yang SUDAH ADA sebelum fitur ini dianggap terverifikasi,
        // supaya tidak ada yang tiba-tiba terkunci. Hanya pendaftar baru yang menunggu.
        DB::table('user')->update([
            'status_akun' => 'terverifikasi',
            'diverifikasi_pada' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropConstrainedForeignId('diverifikasi_oleh');
            $table->dropColumn(['status_akun', 'foto_identitas', 'catatan_verifikasi', 'diverifikasi_pada']);
        });
    }
};