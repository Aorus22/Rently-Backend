<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\LokasiGarasi;

class LokasiGarasiController extends Controller
{
    public function index()
    {
        $lokasiGarasi = LokasiGarasi::all();
        return response()->json($lokasiGarasi);
    }

    public function show($id)
    {
        $lokasigarasi = LokasiGarasi::find($id);
        if (!$lokasigarasi) {
            return response()->json(['message' => 'Lokasi tidak ditemukan'], 404);
        }
        return response()->json($lokasigarasi);
    }
}
