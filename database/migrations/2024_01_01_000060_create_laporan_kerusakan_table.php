<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_kerusakan', function (Blueprint $table) {
            $table->id('id_laporan');
            $table->foreignId('user_id')->constrained('user', 'id_user')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('user', 'id_user')
                ->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('alat_id')->constrained('alat', 'id_alat')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('deskripsi');
            $table->string('foto_bukti')->nullable();
            $table->enum('status', ['diproses', 'selesai'])->default('diproses');
            $table->text('catatan_tindak_lanjut')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_kerusakan');
    }
};
