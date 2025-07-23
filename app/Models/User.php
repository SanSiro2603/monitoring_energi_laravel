<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;  // Tambahkan SoftDeletes

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;  // Tambahkan SoftDeletes

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
    ];

    /**
     * Kolom yang disembunyikan saat serialisasi
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Kolom yang akan dicasting secara otomatis
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Kolom yang termasuk dalam soft delete.
     * Pastikan kolom `deleted_at` ada.
     */
    protected $dates = ['deleted_at'];

    /**
     * Relasi dengan tabel input data
     */
    public function inputs() {
        return $this->hasMany(Input::class);  // Menambahkan relasi dengan Input
    }
}
