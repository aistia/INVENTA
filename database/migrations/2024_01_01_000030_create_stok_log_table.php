<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_log', function (Blueprint $table) {
            $table->id('id_stok');
            $table->foreignId('id_alat')->constrained('alat', 'id_alat')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('admin_id')->constrained('user', 'id_user')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('jenis', ['masuk', 'keluar']);
            $table->unsignedInteger('jumlah');
            $table->string('keterangan')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_log');
    }
};
