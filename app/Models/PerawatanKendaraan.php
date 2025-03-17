<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PerawatanKendaraan extends Model
{
    use HasFactory;

    protected $table = 'perawatan_kendaraan';

    protected $fillable = [
        'kendaraan_id',
        'tanggal_perawatan',
        'jenis_perawatan',
        'biaya_perawatan',
        'bengkel_teknisi',
        'catatan_tambahan'
    ];
}
