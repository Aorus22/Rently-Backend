<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pemesanan;
use App\Models\Pembayaran;

class PembayaranController extends Controller
{
    public function createPayment(Request $request, $id)
    {
        $pemesanan = Pemesanan::findOrFail($id);

        $request->validate([
            'metode_pembayaran' => 'required|in:Transfer Bank,E-Wallet'
        ]);

        $pembayaran = Pembayaran::create([
            'pemesanan_id' => $pemesanan->id,
            'metode_pembayaran' => $request->metode_pembayaran,
            'jumlah_pembayaran' => 0,
            'status_pembayaran' => 'Belum Lunas',
            'deposit_keamanan' => 0,
            'bukti_pembayaran' => null,
            'tanggal_pembayaran' => null,
        ]);

        return response()->json([
            'message' => 'Metode pembayaran berhasil dipilih',
            'pembayaran_id' => $pembayaran->id
        ]);
    }

    public function getPaymentDetail($id)
    {
        $pembayaran = Pembayaran::with('pemesanan.kendaraan')->findOrFail($id);

        return response()->json([
            'pembayaran' => $pembayaran,
            'pemesanan' => $pembayaran->pemesanan
        ]);
    }

    public function uploadBuktiPembayaran(Request $request, $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $pemesanan = $pembayaran->pemesanan;

        // Cek apakah sudah ada bukti pembayaran sebelumnya
        if ($pembayaran->bukti_pembayaran) {
            return response()->json([
                'message' => 'Bukti pembayaran sudah diunggah, tidak dapat mengunggah ulang.'
            ], 400);
        }

        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $file = $request->file('bukti_pembayaran');
        $filePath = $file->store('bukti_pembayaran', 'public');
        $fullPath = asset("storage/$filePath");

        $pembayaran->update([
            'bukti_pembayaran' => $fullPath,
            'status_pembayaran' => 'Pending',
            'tanggal_pembayaran' => now(),
        ]);

      // Update status pemesanan menjadi "Menunggu Konfirmasi"
        $pemesanan->update([
            'status_pemesanan' => 'Menunggu Konfirmasi'
        ]);

        return response()->json([
            'message' => 'Bukti pembayaran berhasil diunggah',
            'bukti_pembayaran' => asset("storage/$filePath"),
            'status_pembayaran' => 'Pending'
        ]);
    }

}
