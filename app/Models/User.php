<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'user';
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'id_role',
        'username',
        'nama',
        'email',
        'password',
        'tipe_user',
        'nis',
        'nip',
        'kelas',
        'jabatan',
        'no_hp',
        'foto',
        'status_akun',
        'foto_identitas',
        'catatan_verifikasi',
        'diverifikasi_oleh',
        'diverifikasi_pada',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'diverifikasi_pada' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    /** Admin yang memverifikasi / menolak akun ini. */
    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh', 'id_user');
    }

    /**
     * Nama tampilan — beberapa bagian framework/Blade mengharapkan atribut "name".
     */
    public function getNameAttribute(): string
    {
        return $this->nama;
    }

    public function isAdmin(): bool
    {
        return $this->role?->nama_role === 'admin';
    }

    public function isSiswa(): bool
    {
        return $this->tipe_user === 'siswa';
    }

    public function isGuru(): bool
    {
        return $this->tipe_user === 'guru';
    }

    /** Admin selalu dianggap sah; siswa/guru harus sudah diverifikasi admin. */
    public function isTerverifikasi(): bool
    {
        return $this->isAdmin() || $this->status_akun === 'terverifikasi';
    }

    /** NIS untuk siswa, NIP untuk guru — dipakai sebagai identitas login. */
    public function getNisNipAttribute(): ?string
    {
        return $this->nis ?: $this->nip;
    }

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class, 'user_id', 'id_user');
    }

    public function laporanKerusakan(): HasMany
    {
        return $this->hasMany(LaporanKerusakan::class, 'user_id', 'id_user');
    }
}