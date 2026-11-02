<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_peminjaman', function (Blueprint $table) {
            $table->id('id_detail');
            $table->foreignId('id_peminjaman')->constrained('peminjaman', 'id_peminjaman')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('id_alat')->constrained('alat', 'id_alat')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedInteger('jumlah')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_peminjaman');
    }
};
