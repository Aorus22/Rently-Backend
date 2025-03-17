<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    use HasFactory;

    protected $table = 'pemesanan';

    protected $fillable = [
        'user_id',
        'kendaraan_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'total_harga_sewa',
        'status_pemesanan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class);
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'pemesanan_id');
    }
}
