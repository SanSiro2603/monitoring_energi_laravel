<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Kolom-kolom yang bisa diisi secara massal
     */
    protected $fillable = [
    'name',
    'username',
    'email',
    'password',
    'unit_bagian',
    'level_gol',
    'wilayah',
    'unit_kerja',
    'foto',
    'role',
];

    /**
     * Kolom yang disembunyikan saat serialisasi (misal ke JSON)
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting otomatis tipe data kolom
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
