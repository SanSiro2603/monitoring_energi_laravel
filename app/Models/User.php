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
     * Kolom-kolom yang dapat diisi secara massal
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'unit_bagian',
        'jabatan',
        'wilayah',
        'unit_kerja',
        'foto',
        'role',
        'otp_code',           // ✅ Tambahan untuk kode OTP
        'otp_expires_at',     // ✅ Tambahan untuk batas waktu OTP
        'email_verified_at',  // ✅ Bawaan Laravel untuk verifikasi email
    ];

    /**
     * Kolom yang disembunyikan saat serialisasi
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp_code', // ✅ Jangan tampilkan kode OTP saat serialisasi
    ];

    /**
     * Kolom yang akan dicasting secara otomatis
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime', // ✅ Cast ke datetime
        'password' => 'hashed',
    ];
}
