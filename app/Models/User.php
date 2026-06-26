<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\Inspeksi;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone_number',
        'status_pengerjaan',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function inspections()
    {
        return $this->hasMany(Inspeksi::class, 'petugas_id');
    }

    public function facilities()
    {
        return $this->hasMany(Fasilitas::class, 'penanggung_jawab');
    }

    public function facilitiesTambahan()
    {
        return $this->belongsToMany(Fasilitas::class, 'fasilitas_petugas', 'user_id', 'fasilitas_id');
    }

    public function allFacilities()
    {
        $utamaIds = $this->facilities()->pluck('fasilitas.id');
        $tambahanIds = $this->facilitiesTambahan()->pluck('fasilitas.id');
        $allIds = $utamaIds->merge($tambahanIds)->unique();
        return Fasilitas::whereIn('id', $allIds);
    }
}
