<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KontrakSewa extends Model
{
    use HasFactory;

    protected $table = 'kontrak_sewa';

    protected $fillable = [
        'pemesanan_id',
        'link_kontrak',
        'status_kontrak',
    ];

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class, 'pemesanan_id');
    }
}
