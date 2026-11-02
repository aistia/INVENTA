<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user', function (Blueprint $table) {
            $table->id('id_user');
            $table->foreignId('id_role')->constrained('roles', 'id_role')
                ->cascadeOnUpdate()->restrictOnDelete();
            $table->string('username', 50)->unique();
            $table->string('nama', 100);
            $table->string('email', 150)->unique();
            $table->string('password');
            $table->enum('tipe_user', ['siswa', 'guru'])->nullable();
            $table->string('nis', 30)->nullable();
            $table->string('nip', 30)->nullable();
            $table->string('kelas', 30)->nullable();
            $table->string('jabatan', 100)->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('foto')->nullable();
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
        });

        // Dibutuhkan oleh fitur bawaan Laravel (lupa password & session DB).
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('user');
    }
};
