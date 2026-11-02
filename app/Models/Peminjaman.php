<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Peminjaman extends Model
{
    /**
     * Batas jam pengembalian untuk peminjaman 1 hari
     * (tanggal pinjam sama dengan tanggal kembali). Format 24 jam.
     */
    public const JAM_BATAS_SATU_HARI = '15:30';

    protected $table = 'peminjaman';
    protected $primaryKey = 'id_peminjaman';

    protected $fillable = [
        'user_id',
        'admin_id',
        'deskripsi',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'tanggal_kembali_aktual',
        'foto_pengembalian',
        'catatan_pengembalian',
        'diajukan_kembali_pada',
        'status',
        'arsip',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'date',
            'tanggal_kembali_rencana' => 'date',
            'tanggal_kembali_aktual' => 'date',
            'diajukan_kembali_pada' => 'datetime',
        ];
    }

    public function isJatuhTempoHariIni(): bool
    {
        return $this->tanggal_kembali_rencana?->isToday() ?? false;
    }

    /** Dipinjam dan dikembalikan di hari yang sama. */
    public function isPinjamSatuHari(): bool
    {
        return $this->tanggal_pinjam !== null
            && $this->tanggal_kembali_rencana !== null
            && $this->tanggal_pinjam->isSameDay($this->tanggal_kembali_rencana);
    }

    /** Batas waktu kembali (tanggal + jam) khusus peminjaman 1 hari; null jika bukan 1 hari. */
    public function batasKembali(): ?Carbon
    {
        if (! $this->isPinjamSatuHari()) {
            return null;
        }

        return $this->tanggal_kembali_rencana
            ->copy()
            ->setTimeFromTimeString(self::JAM_BATAS_SATU_HARI);
    }

    /** Jam batas dalam format tampilan, mis. "15.30". */
    public static function jamBatasTampil(): string
    {
        return str_replace(':', '.', self::JAM_BATAS_SATU_HARI);
    }

    public function isTerlambat(): bool
    {
        if ($this->status !== 'disetujui' || ! $this->tanggal_kembali_rencana) {
            return false;
        }

        // Peminjaman 1 hari: terlambat bila lewat jam batas.
        // Kalau siswa sudah mengajukan pengembalian, yang dibandingkan adalah
        // waktu pengajuannya, bukan waktu admin mengecek.
        $batas = $this->batasKembali();

        if ($batas) {
            $acuan = $this->diajukan_kembali_pada ?? now();

            return $acuan->gt($batas);
        }

        // Peminjaman lebih dari 1 hari: terlambat bila tanggal kembali sudah lewat
        // (bukan hari ini).
        return $this->tanggal_kembali_rencana->isPast()
            && ! $this->tanggal_kembali_rencana->isToday();
    }

    /** Siswa sudah mengunggah bukti, tinggal menunggu admin mengecek & mengonfirmasi. */
    public function menungguKonfirmasiKembali(): bool
    {
        return $this->status === 'disetujui' && $this->diajukan_kembali_pada !== null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id', 'id_user');
    }

    public function detail(): HasMany
    {
        return $this->hasMany(DetailPeminjaman::class, 'id_peminjaman', 'id_peminjaman');
    }
}