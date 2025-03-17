<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    use HasFactory;

    protected $table = 'kendaraan';

    protected $fillable = [
        'gambar_url',
        'kategori_kendaraan',
        'merek_model',
        'kapasitas_kursi',
        'jenis_transmisi',
        'tahun_produksi',
        'nomor_polisi',
        'status_ketersediaan',
        'harga_sewa_per_periode',
        'kondisi_fasilitas',
        'lokasi_garasi_id',
    ];

    public function lokasiGarasi()
    {
        return $this->belongsTo(LokasiGarasi::class, 'lokasi_garasi_id');
    }
}
