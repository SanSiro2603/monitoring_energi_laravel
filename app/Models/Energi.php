<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Energi extends Model
{
    use HasFactory;

    protected $fillable = [
        'kantor',
        'bulan',
        'tahun',
        'listrik',
        'daya_listrik',
        'air',
        'kertas',
        'pertalite',
        'pertamax',
        'solar',
        'dexlite',
        'pertamina_dex',
        'bbm',
        'jenis_bbm',
        'user_id'
    ];

    protected $casts = [
        'listrik' => 'float',
        'daya_listrik' => 'float',
        'air' => 'float',
        'kertas' => 'float',
        'pertalite' => 'float',
        'pertamax' => 'float',
        'solar' => 'float',
        'dexlite' => 'float',
        'pertamina_dex' => 'float',
        'bbm' => 'float',
        'tahun' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor to get total BBM (calculated from individual fuel types)
    public function getTotalBbmAttribute()
    {
        return ($this->pertalite ?? 0) + 
               ($this->pertamax ?? 0) + 
               ($this->solar ?? 0) + 
               ($this->dexlite ?? 0) + 
               ($this->pertamina_dex ?? 0);
    }

    // Accessor to get fuel types array
    public function getFuelTypesAttribute()
    {
        $types = [];
        if (($this->pertalite ?? 0) > 0) $types[] = 'Pertalite';
        if (($this->pertamax ?? 0) > 0) $types[] = 'Pertamax';
        if (($this->solar ?? 0) > 0) $types[] = 'Solar';
        if (($this->dexlite ?? 0) > 0) $types[] = 'Dexlite';
        if (($this->pertamina_dex ?? 0) > 0) $types[] = 'Pertamina Dex';
        return $types;
    }
}