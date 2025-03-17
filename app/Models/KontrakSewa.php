<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KontrakSewa extends Model
{
    use HasFactory;

    protected $table = 'kontrak_sewa';

    protected $fillable = [
        'pemesanan_id',
        'detail_perjanjian',
        'tanda_tangan_digital',
        'status_kontrak',
    ];
}
