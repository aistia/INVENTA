<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanKerusakan extends Model
{
    protected $table = 'laporan_kerusakan';
    protected $primaryKey = 'id_laporan';

    protected $fillable = [
        'user_id',
        'admin_id',
        'alat_id',
        'jenis_penanganan',
        'jumlah_unit',
        'deskripsi',
        'foto_bukti',
        'status',
        'catatan_tindak_lanjut',
        'arsip',
    ];

    protected $casts = [
        'jumlah_unit' => 'integer',
        'arsip' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id', 'id_user');
    }

    public function alat(): BelongsTo
    {
        return $this->belongsTo(Alat::class, 'alat_id', 'id_alat');
    }

    public function kodeLaporanLabel(): string
    {
        return 'LKR-'.str_pad((string) $this->id_laporan, 4, '0', STR_PAD_LEFT);
    }
}