<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alat', function (Blueprint $table) {
            $table->boolean('arsip')->default(false)->after('kondisi');
        });

        Schema::table('peminjaman', function (Blueprint $table) {
            $table->boolean('arsip')->default(false)->after('status');
        });

        Schema::table('laporan_kerusakan', function (Blueprint $table) {
            $table->boolean('arsip')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('alat', fn (Blueprint $table) => $table->dropColumn('arsip'));
        Schema::table('peminjaman', fn (Blueprint $table) => $table->dropColumn('arsip'));
        Schema::table('laporan_kerusakan', fn (Blueprint $table) => $table->dropColumn('arsip'));
    }
};