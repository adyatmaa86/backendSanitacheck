<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporans';

    protected $fillable = [
        'nama_pelapor',
        'no_telp',
        'fasilitas_id',
        'petugas_id',
        'keluhan',
        'foto_bukti',
        'status',
        'catatan_admin',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function facility()
    {
        return $this->belongsTo(Fasilitas::class, 'fasilitas_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function inspeksi()
    {
        return $this->hasOne(Inspeksi::class, 'laporan_id');
    }
}
