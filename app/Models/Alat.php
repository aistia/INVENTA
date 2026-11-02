<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alat extends Model
{
    protected $table = 'alat';
    protected $primaryKey = 'id_alat';

    protected $fillable = [
        'id_kategori',
        'kode_alat',
        'nama_alat',
        'deskripsi',
        'foto',
        'jumlah_stok',
        'jumlah_servis',
        'jumlah_rusak',
        'kondisi',
        'arsip',
    ];

    protected function casts(): array
    {
        return ['arsip' => 'boolean'];
    }

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? asset('storage/'.$this->foto) : null;
    }

    /** Jumlah unit yang sedang tidak bisa dipinjam (perlu servis + rusak). */
    public function getUnitBermasalahAttribute(): int
    {
        return (int) $this->jumlah_servis + (int) $this->jumlah_rusak;
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriAlat::class, 'id_kategori', 'id_kategori');
    }

    public function stokLog(): HasMany
    {
        return $this->hasMany(StokLog::class, 'id_alat', 'id_alat');
    }

    public function detailPeminjaman(): HasMany
    {
        return $this->hasMany(DetailPeminjaman::class, 'id_alat', 'id_alat');
    }

    public function laporanKerusakan(): HasMany
    {
        return $this->hasMany(LaporanKerusakan::class, 'alat_id', 'id_alat');
    }
}