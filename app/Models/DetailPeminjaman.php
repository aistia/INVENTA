<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPeminjaman extends Model
{
    public $timestamps = false;

    protected $table = 'detail_peminjaman';
    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'id_peminjaman',
        'id_alat',
        'jumlah',
        'jumlah_baik_kembali',
        'jumlah_servis_kembali',
        'jumlah_rusak_kembali',
    ];

    public function peminjaman(): BelongsTo
    {
        return $this->belongsTo(
            Peminjaman::class,
            'id_peminjaman',
            'id_peminjaman'
        );
    }

    public function alat(): BelongsTo
    {
        return $this->belongsTo(
            Alat::class,
            'id_alat',
            'id_alat'
        );
    }
}
