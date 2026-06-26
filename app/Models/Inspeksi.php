<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspeksi extends Model
{
    use HasFactory;

    protected $table = 'inspeksis';

    protected $fillable = [
        'fasilitas_id',
        'petugas_id',
        'tanggal_inspeksi',
        'kondisi_kebersihan',
        'ketersediaan_air',
        'ketersediaan_sabun',
        'bau_tidak_sedap',
        'catatan',
        'foto',
        'foto_selesai',
        'status_tindak_lanjut',
        'is_completed',
    ];

    protected $casts = [
        'tanggal_inspeksi' => 'datetime',
        'is_completed' => 'boolean',
    ];

    public function facility()
    {
        return $this->belongsTo(Fasilitas::class, 'fasilitas_id');
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function getScoreAttribute()
    {
        $score = 0;
        
        // Kondisi Kebersihan (Max 40 points)
        if ($this->kondisi_kebersihan === 'baik') {
            $score += 40;
        } elseif ($this->kondisi_kebersihan === 'cukup') {
            $score += 20;
        }
        
        // Ketersediaan Air (Max 20 points)
        if ($this->ketersediaan_air === 'tersedia') {
            $score += 20;
        }
        
        // Ketersediaan Sabun (Max 20 points)
        if ($this->ketersediaan_sabun === 'tersedia') {
            $score += 20;
        }
        
        // Bau Tidak Sedap (Max 20 points)
        if ($this->bau_tidak_sedap === 'tidak') {
            $score += 20;
        }
        
        return $score;
    }
}
