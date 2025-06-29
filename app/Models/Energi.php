<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Energi extends Model
{
    use HasFactory;

    protected $table = 'energis'; // nama tabel (jamak)

   protected $fillable = [
    'kantor', 'bulan', 'tahun', 'listrik', 'daya_listrik',
    'air', 'bbm', 'jenis_bbm', 'kertas'
];
}

