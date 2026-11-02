<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id('id_peminjaman');
            $table->foreignId('user_id')->constrained('user', 'id_user')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('user', 'id_user')
                ->cascadeOnUpdate()->nullOnDelete();
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak', 'dikembalikan'])
                ->default('menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
