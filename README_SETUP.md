# INVENTA — Setup Project Laravel (Tahap 1: Login & Register)

Skema database sudah disesuaikan 1:1 dengan `inventa_database_v2.sql` yang kamu berikan
(tabel `roles`, `user`, `kategori_alat`, `alat`, `stok_log`, `peminjaman`,
`detail_peminjaman`, `laporan_kerusakan`, lengkap dengan trigger MySQL-nya).
Fitur yang sudah jalan di tahap ini: **Login** dan **Register** (siswa/guru).
Modul lain (dashboard, alat, peminjaman, laporan) masih placeholder — akan
dibangun bertahap.

## Langkah instalasi (di komputer kamu, bukan di sandbox ini)

1. Pastikan sudah ada PHP 8.2+, Composer, dan MySQL (mis. Laragon/XAMPP).
2. Ekstrak project ini, lalu buka terminal di folder project:
   ```bash
   composer install
   ```
3. File `.env` sudah dibuat otomatis (disalin dari `.env.example`) dengan:
   - `DB_CONNECTION=mysql`
   - `DB_DATABASE=inventa_db`
   - `DB_USERNAME=root`
   - `DB_PASSWORD=` (kosongkan/isi sesuai MySQL kamu)

   Sesuaikan kalau perlu, lalu generate key aplikasi:
   ```bash
   php artisan key:generate
   ```
4. Buat database (kalau belum ada):
   ```bash
   mysql -u root -e "CREATE DATABASE IF NOT EXISTS inventa_db"
   ```
5. Jalankan migration + seeder (otomatis buat semua tabel + trigger MySQL + akun demo):
   ```bash
   php artisan migrate --seed
   ```
6. Jalankan server:
   ```bash
   php artisan serve
   ```
   Buka `http://127.0.0.1:8000` — otomatis diarahkan ke halaman **Login**.

## Akun demo (dari seeder)

| Role   | Username     | Password   |
|--------|-------------|------------|
| Admin  | `admin.lab`  | `password` |
| Siswa  | `siswa.demo` | `password` |
| Guru   | `guru.demo`  | `password` |

Login bisa pakai **username atau email**, keduanya dicek otomatis.

## Catatan penting

- **Trigger MySQL**: migration `2024_01_01_000070_create_inventa_triggers.php`
  hanya berjalan kalau `DB_CONNECTION=mysql`. Kalau nanti kamu pakai SQLite
  untuk development cepat, trigger ini otomatis dilewati (tidak error) —
  tapi logika stok otomatis (trigger 2 & 3) belum direplikasi di kode PHP
  karena modul peminjaman belum dibangun. Ini akan ditangani saat modul
  peminjaman & stok dibuat di tahap berikutnya.
- Struktur tabel & nama kolom (id_user, id_role, nis, nip, tipe_user, dst)
  mengikuti persis skema SQL yang kamu kirim, supaya kalau ada tugas/dosen
  yang cek ERD, strukturnya cocok.
- Registrasi mandiri hanya untuk **siswa/guru** (otomatis dapat `id_role`
  = "user"). Akun **admin** sengaja tidak bisa daftar sendiri — hanya lewat
  seeder atau nanti dibuatkan menu khusus oleh admin lain.

## Lanjutan

Kabari saja modul berikutnya yang mau dibangun (dashboard siswa, daftar alat,
form peminjaman, dst) — akan dikerjakan satu per satu seperti yang kamu minta.
