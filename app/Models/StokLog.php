<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokLog extends Model
{
    public $timestamps = false;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;

    protected $table = 'stok_log';
    protected $primaryKey = 'id_stok';

    protected $fillable = [
        'id_alat',
        'admin_id',
        'jenis',
        'jumlah',
        'keterangan',
    ];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function alat(): BelongsTo
    {
        return $this->belongsTo(Alat::class, 'id_alat', 'id_alat');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id', 'id_user');
    }
}
