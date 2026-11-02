```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_peminjaman', function (Blueprint $table) {
            $table->unsignedInteger('jumlah_baik_kembali')->nullable();
            $table->unsignedInteger('jumlah_servis_kembali')->nullable();
            $table->unsignedInteger('jumlah_rusak_kembali')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('detail_peminjaman', function (Blueprint $table) {
            $table->dropColumn([
                'jumlah_baik_kembali',
                'jumlah_servis_kembali',
                'jumlah_rusak_kembali',
            ]);
        });
    }
};
