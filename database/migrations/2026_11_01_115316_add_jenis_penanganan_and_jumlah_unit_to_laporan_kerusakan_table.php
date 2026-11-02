<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan_kerusakan', function (Blueprint $table) {
            $table->enum('jenis_penanganan', ['servis', 'rusak'])
                ->nullable()
                ->after('alat_id');

            $table->unsignedInteger('jumlah_unit')
                ->default(1)
                ->after('jenis_penanganan');
        });
    }

    public function down(): void
    {
        Schema::table('laporan_kerusakan', function (Blueprint $table) {
            $table->dropColumn(['jenis_penanganan', 'jumlah_unit']);
        });
    }
};