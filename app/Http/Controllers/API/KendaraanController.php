<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kendaraan;

class KendaraanController extends Controller
{
    // Mengambil semua data kendaraan
    public function index()
    {
        $kendaraan = Kendaraan::with('lokasiGarasi')->get();
        return response()->json($kendaraan);
    }

    // Menyimpan data kendaraan baru
    public function store(Request $request)
    {
        $request->validate([
            'gambar_url' => 'required|string',
            'kategori_kendaraan' => 'required|in:Mobil,Minibus,Pickup',
            'merek_model' => 'required|string',
            'kapasitas_kursi' => 'required|integer',
            'jenis_transmisi' => 'required|in:Manual,Automatic',
            'tahun_produksi' => 'required|integer',
            'nomor_polisi' => 'required|string|unique:kendaraan',
            'status_ketersediaan' => 'required|in:Tersedia,Disewa,Perawatan',
            'harga_sewa_per_periode' => 'required|numeric',
            'kondisi_fasilitas' => 'required|string',
            'lokasi_garasi_id' => 'required|exists:garasi,id',
        ]);

        $kendaraan = Kendaraan::create($request->all());
        return response()->json(['message' => 'Kendaraan berhasil ditambahkan!', 'data' => $kendaraan]);
    }

    // Mengambil detail kendaraan berdasarkan ID
    public function show($id)
    {
        $kendaraan = Kendaraan::with('lokasiGarasi')->find($id);
        if (!$kendaraan) {
            return response()->json(['message' => 'Kendaraan tidak ditemukan'], 404);
        }
        return response()->json($kendaraan);
    }

    // Mengupdate data kendaraan
    public function update(Request $request, $id)
    {
        $kendaraan = Kendaraan::find($id);
        if (!$kendaraan) {
            return response()->json(['message' => 'Kendaraan tidak ditemukan'], 404);
        }

        $kendaraan->update($request->all());
        return response()->json(['message' => 'Kendaraan berhasil diperbarui!', 'data' => $kendaraan]);
    }

    // Menghapus kendaraan
    public function destroy($id)
    {
        $kendaraan = Kendaraan::find($id);
        if (!$kendaraan) {
            return response()->json(['message' => 'Kendaraan tidak ditemukan'], 404);
        }

        $kendaraan->delete();
        return response()->json(['message' => 'Kendaraan berhasil dihapus!']);
    }
}
