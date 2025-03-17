<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PelacakanKendaraan extends Model
{
    use HasFactory;

    protected $table = 'pelacakan_kendaraan';

    protected $fillable = [
        'kendaraan_id',
        'lokasi_mobil_digunakan',
        'status_kondisi_setelah_sewa',
    ];
}
