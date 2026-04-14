<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // ✅ Tambahin ini karena PK kamu bukan 'id'
    protected $primaryKey = 'id_user';

    // (opsional tapi aman)
    public $incrementing = true;
    protected $keyType = 'int';

    /**
     * Mass assignable
     */
    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'kelas',
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function pengaduan()
    {
        return $this->hasMany(Pengaduan::class, 'id_user');
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class, 'dibuat_oleh');
    }
}