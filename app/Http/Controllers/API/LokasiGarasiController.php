<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\LokasiGarasi;
use Illuminate\Http\Request;
use App\Models\Kendaraan;

class LokasiGarasiController extends Controller
{
    public function index()
    {
        $lokasiGarasi = LokasiGarasi::all();
        return response()->json($lokasiGarasi);
    }
}
