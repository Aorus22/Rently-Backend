<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable = [
        'pemesanan_id',
        'metode_pembayaran',
        'jumlah_pembayaran',
        'tanggal_pembayaran',
        'status_pembayaran',
        'deposit_keamanan',
        'bukti_pembayaran'
    ];

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class);
    }
}
