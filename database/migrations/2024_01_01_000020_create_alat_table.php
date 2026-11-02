<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alat', function (Blueprint $table) {
            $table->id('id_alat');
            $table->foreignId('id_kategori')->constrained('kategori_alat', 'id_kategori')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('kode_alat', 50)->unique();
            $table->string('nama_alat', 150);
            $table->text('deskripsi')->nullable();
            $table->unsignedInteger('jumlah_stok')->default(0);
            $table->enum('kondisi', ['baik', 'perlu_servis', 'rusak'])->default('baik');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alat');
    }
};
