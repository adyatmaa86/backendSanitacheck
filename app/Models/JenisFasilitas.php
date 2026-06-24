<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisFasilitas extends Model
{
    protected $table = 'jenis_fasilitas';
    protected $fillable = ['nama_jenis', 'slug'];

    public function fasilitas()
    {
        return $this->hasMany(Fasilitas::class, 'jenis_fasilitas', 'slug');
    }
}
