<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiGarasi extends Model
{
    use HasFactory;

    protected $table = 'lokasi_garasi';

    protected $fillable = [
        'kota',
        'alamat',
        'latitude',
        'longitude',
    ];

    public function kendaraan()
    {
        return $this->hasMany(Kendaraan::class, 'lokasi_garasi_id');
    }
}
