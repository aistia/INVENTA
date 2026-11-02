
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_peminjaman_dikembalikan');

        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_peminjaman_dikembalikan
AFTER UPDATE ON peminjaman
FOR EACH ROW
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE v_id_alat INT;
    DECLARE v_jumlah_baik INT DEFAULT 0;

    DECLARE cur CURSOR FOR
        SELECT id_alat, COALESCE(jumlah_baik_kembali, 0)
        FROM detail_peminjaman
        WHERE id_peminjaman = NEW.id_peminjaman;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    IF NEW.status = 'dikembalikan'
       AND OLD.status <> 'dikembalikan' THEN

        OPEN cur;

        read_loop: LOOP
            FETCH cur INTO v_id_alat, v_jumlah_baik;

            IF done = 1 THEN
                LEAVE read_loop;
            END IF;

            IF v_jumlah_baik > 0 THEN
                INSERT INTO stok_log
                    (id_alat, admin_id, jenis, jumlah, keterangan)
                VALUES (
                    v_id_alat,
                    NEW.admin_id,
                    'masuk',
                    v_jumlah_baik,
                    CONCAT(
                        'Pengembalian peminjaman #',
                        NEW.id_peminjaman,
                        ' - barang kondisi baik'
                    )
                );
            END IF;
        END LOOP;

        CLOSE cur;
    END IF;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_peminjaman_dikembalikan');

        DB::unprepared(<<<'SQL'
CREATE TRIGGER trg_peminjaman_dikembalikan
AFTER UPDATE ON peminjaman
FOR EACH ROW
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE v_id_alat INT;
    DECLARE v_jumlah INT;

    DECLARE cur CURSOR FOR
        SELECT id_alat, jumlah
        FROM detail_peminjaman
        WHERE id_peminjaman = NEW.id_peminjaman;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    IF NEW.status = 'dikembalikan'
       AND OLD.status <> 'dikembalikan' THEN

        OPEN cur;

        read_loop: LOOP
            FETCH cur INTO v_id_alat, v_jumlah;

            IF done = 1 THEN
                LEAVE read_loop;
            END IF;

            INSERT INTO stok_log
                (id_alat, admin_id, jenis, jumlah, keterangan)
            VALUES (
                v_id_alat,
                NEW.admin_id,
                'masuk',
                v_jumlah,
                CONCAT('Pengembalian peminjaman #', NEW.id_peminjaman)
            );
        END LOOP;

        CLOSE cur;
    END IF;
END
SQL);
    }
};
