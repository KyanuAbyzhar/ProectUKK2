<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    protected $table = 'pengaduan';
    protected $primaryKey = 'id_pengaduan';

    protected $fillable = [
        'id_user',
        'id_kategori',
        'judul',
        'deskripsi',
        'tanggal_pengaduan',
        'status',
        'foto'
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class, 'id_pengaduan');
    }
}
