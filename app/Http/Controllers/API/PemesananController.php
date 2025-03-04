<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PemesananController extends Controller
{
    // Ambil semua pemesanan untuk admin/user
    public function index()
    {
        $user = Auth::user();

        $pemesanan = Pemesanan::where('user_id', $user->id)
            ->with('kendaraan') // Include data kendaraan
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($pemesanan);
    }

    // Buat pemesanan baru
    public function store(Request $request)
    {
        $request->validate([
            'kendaraan_id' => 'required|exists:kendaraan,id',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'durasi' => 'required|integer|min:1'
        ]);

        $kendaraan = Kendaraan::findOrFail($request->kendaraan_id);

        // Hitung total harga
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalSelesai = date('Y-m-d', strtotime("+{$request->durasi} days", strtotime($tanggalMulai)));
        $totalHarga = $kendaraan->harga_sewa_per_periode * $request->durasi;

        // Simpan ke database
        $pemesanan = Pemesanan::create([
            'user_id' => Auth::id(),
            'kendaraan_id' => $request->kendaraan_id,
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'total_harga_sewa' => $totalHarga,
            'status_pemesanan' => 'Menunggu Pembayaran',
        ]);

        return response()->json(['message' => 'Pemesanan berhasil dibuat', 'pemesanan' => $pemesanan], 201);
    }

    // Ambil detail pemesanan
    public function show($id)
    {
        $pemesanan = Pemesanan::with('kendaraan')->findOrFail($id);

        if ($pemesanan->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($pemesanan);
    }

    // Batalkan pemesanan
    public function cancel($id)
    {
        $pemesanan = Pemesanan::findOrFail($id);

        if ($pemesanan->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $pemesanan->update(['status_pemesanan' => 'Dibatalkan']);

        return response()->json(['message' => 'Pemesanan berhasil dibatalkan']);
    }

    // Melakukan pembayaran 
    public function bayar(Request $request, $id)
{
    $pemesanan = Pemesanan::findOrFail($id);

    if ($request->metode_pembayaran === 'transfer') {
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran');

        $pemesanan->update([
            'status_pemesanan' => 'Menunggu Konfirmasi',
            'bukti_pembayaran' => $path,
        ]);
    } else {
        $pemesanan->update([
            'status_pemesanan' => 'Dikonfirmasi',
        ]);
    }

    return response()->json(['message' => 'Pembayaran berhasil dikonfirmasi']);
}

}
