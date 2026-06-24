<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{
    use HasFactory;

    protected $table = 'fasilitas';

    protected $fillable = [
        'nama_fasilitas',
        'jenis_fasilitas',
        'lokasi',
        'penanggung_jawab',
        'status_aktif',
        'foto_before',
        'foto_after',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
        // 'penanggung_jawab' => 'integer',
    ];

    public function petugas()
    {
        return $this->belongsTo(User::class, 'penanggung_jawab');
    }

    public function jenis()
    {
        return $this->belongsTo(JenisFasilitas::class, 'jenis_fasilitas', 'slug');
    }

    public function inspections()
    {
        return $this->hasMany(Inspeksi::class, 'fasilitas_id');
    }

    public function latestInspection()
    {
        return $this->hasOne(Inspeksi::class, 'fasilitas_id')->latestOfMany();
    }

    public function getCleanlinessStatusAttribute()
    {
        $latest = $this->latestInspection;
        if (!$latest) {
            return 'bersih';
        }

        // If status_tindak_lanjut is perlu perbaikan -> buruk
        // Prioritaskan kebersihan buruk atau perlu perbaikan (keduanya masuk status 'buruk')
        if ($latest->status_tindak_lanjut === 'perlu perbaikan' || $latest->kondisi_kebersihan === 'buruk') {
            return 'buruk';
        }

        // Baru kemudian cek jika perlu dibersihkan atau kebersihan cukup (keduanya masuk status 'perlu dibersihkan')
        if ($latest->status_tindak_lanjut === 'perlu dibersihkan' || $latest->kondisi_kebersihan === 'cukup') {
            return 'perlu dibersihkan';
        }

        return 'bersih';
    }
}
