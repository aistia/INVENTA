<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Trigger-trigger ini mereplikasi logika otomatis dari desain awal
 * (ERD_INVENTA_drawio). Hanya berjalan di MySQL/MariaDB — pada SQLite
 * (dipakai untuk development lokal cepat) migration ini otomatis dilewati,
 * dan sebagai gantinya logika yang sama dijalankan lewat Eloquent model
 * events di aplikasi (lihat app/Models).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_stok_log_after_insert
            AFTER INSERT ON stok_log
            FOR EACH ROW
            BEGIN
                IF NEW.jenis = 'masuk' THEN
                    UPDATE alat SET jumlah_stok = jumlah_stok + NEW.jumlah WHERE id_alat = NEW.id_alat;
                ELSE
                    UPDATE alat SET jumlah_stok = jumlah_stok - NEW.jumlah WHERE id_alat = NEW.id_alat;
                END IF;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_peminjaman_disetujui
            AFTER UPDATE ON peminjaman
            FOR EACH ROW
            BEGIN
                DECLARE done INT DEFAULT 0;
                DECLARE v_id_alat INT;
                DECLARE v_jumlah INT;
                DECLARE cur CURSOR FOR
                    SELECT id_alat, jumlah FROM detail_peminjaman WHERE id_peminjaman = NEW.id_peminjaman;
                DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

                IF NEW.status = 'disetujui' AND OLD.status <> 'disetujui' THEN
                    OPEN cur;
                    read_loop: LOOP
                        FETCH cur INTO v_id_alat, v_jumlah;
                        IF done = 1 THEN
                            LEAVE read_loop;
                        END IF;
                        INSERT INTO stok_log (id_alat, admin_id, jenis, jumlah, keterangan)
                        VALUES (v_id_alat, NEW.admin_id, 'keluar', v_jumlah,
                                CONCAT('Peminjaman #', NEW.id_peminjaman, ' disetujui'));
                    END LOOP;
                    CLOSE cur;
                END IF;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_peminjaman_dikembalikan
            AFTER UPDATE ON peminjaman
            FOR EACH ROW
            BEGIN
                DECLARE done INT DEFAULT 0;
                DECLARE v_id_alat INT;
                DECLARE v_jumlah INT;
                DECLARE cur CURSOR FOR
                    SELECT id_alat, jumlah FROM detail_peminjaman WHERE id_peminjaman = NEW.id_peminjaman;
                DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

                IF NEW.status = 'dikembalikan' AND OLD.status <> 'dikembalikan' THEN
                    OPEN cur;
                    read_loop: LOOP
                        FETCH cur INTO v_id_alat, v_jumlah;
                        IF done = 1 THEN
                            LEAVE read_loop;
                        END IF;
                        INSERT INTO stok_log (id_alat, admin_id, jenis, jumlah, keterangan)
                        VALUES (v_id_alat, NEW.admin_id, 'masuk', v_jumlah,
                                CONCAT('Pengembalian peminjaman #', NEW.id_peminjaman));
                    END LOOP;
                    CLOSE cur;
                END IF;
            END
        SQL);

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

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared('DROP TRIGGER IF EXISTS trg_stok_log_after_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_peminjaman_disetujui');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_peminjaman_dikembalikan');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_laporan_kerusakan_baru');
    }
};
