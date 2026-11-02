<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->string('foto_pengembalian')->nullable()->after('tanggal_kembali_aktual');
            $table->text('catatan_pengembalian')->nullable()->after('foto_pengembalian');
            $table->timestamp('diajukan_kembali_pada')->nullable()->after('catatan_pengembalian');
        });

        // Data lama yang sudah "dikembalikan" tapi belum punya tanggal kembali aktual:
        // isi dengan tanggal terakhir diubah, supaya statistik "tepat waktu" tidak keliru.
        DB::table('peminjaman')
            ->where('status', 'dikembalikan')
            ->whereNull('tanggal_kembali_aktual')
            ->update(['tanggal_kembali_aktual' => DB::raw('DATE(updated_at)')]);
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn(['foto_pengembalian', 'catatan_pengembalian', 'diajukan_kembali_pada']);
        });
    }
};