<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Trigger trg_laporan_kerusakan_baru mengubah alat.kondisi menjadi
 * 'perlu_servis' setiap ada laporan kerusakan baru. Karena kolom kondisi
 * berlaku untuk SELURUH unit alat, satu unit servis membuat unit yang sehat
 * ikut tidak bisa dipinjam.
 *
 * Status servis/rusak sekarang dicatat per unit lewat kolom
 * alat.jumlah_servis dan alat.jumlah_rusak, jadi trigger ini dihapus.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared('DROP TRIGGER IF EXISTS trg_laporan_kerusakan_baru');
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_laporan_kerusakan_baru
            AFTER INSERT ON laporan_kerusakan
            FOR EACH ROW
            BEGIN
                UPDATE alat SET kondisi = 'perlu_servis'
                WHERE id_alat = NEW.alat_id AND kondisi = 'baik';
            END
        SQL);
    }
};