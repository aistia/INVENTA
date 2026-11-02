<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->date('tanggal_pinjam')->nullable()->after('deskripsi');
            $table->date('tanggal_kembali_rencana')->nullable()->after('tanggal_pinjam');
            $table->date('tanggal_kembali_aktual')->nullable()->after('tanggal_kembali_rencana');
        });
    }

    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn(['tanggal_pinjam', 'tanggal_kembali_rencana', 'tanggal_kembali_aktual']);
        });
    }
};