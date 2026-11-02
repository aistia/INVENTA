<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alat', function (Blueprint $table) {
            // Unit yang tidak ikut stok tersedia karena perlu servis / rusak.
            // jumlah_stok tetap berarti "unit yang siap dipinjam".
            $table->unsignedInteger('jumlah_servis')->default(0);
            $table->unsignedInteger('jumlah_rusak')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('alat', function (Blueprint $table) {
            $table->dropColumn(['jumlah_servis', 'jumlah_rusak']);
        });
    }
};