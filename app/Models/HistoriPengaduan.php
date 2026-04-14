<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoriPengaduan extends Model
{
    protected $table = 'histori_pengaduan';
    protected $primaryKey = 'id_histori';

    protected $fillable = [
        'id_pengaduan',
        'status',
        'keterangan',
        'tanggal_update'
    ];

    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class, 'id_pengaduan');
    }
}
