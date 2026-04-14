<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';
    protected $primaryKey = 'id_feedback';

    protected $fillable = [
        'id_pengaduan',
        'dibuat_oleh',
        'pesan_feedback',
        'tanggal_feedback'
    ];

    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class, 'id_pengaduan');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh', 'id_user');
    }
}
