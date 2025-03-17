<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pemesanan;
use App\Models\Pembayaran;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

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

        if ($pembayaran->bukti_pembayaran) {
            return response()->json([
                'message' => 'Bukti pembayaran sudah diunggah, tidak dapat mengunggah ulang.'
            ], 400);
        }

        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096'
        ]);

        $uploadResult = Cloudinary::upload($request->file('bukti_pembayaran')->getRealPath(), [
            'folder' => 'bukti_pembayaran'
        ]);

        $pembayaran->update([
            'bukti_pembayaran' => $uploadResult->getSecurePath(),
            'status_pembayaran' => 'Pending',
            'tanggal_pembayaran' => now(),
        ]);

        $pemesanan->update([
            'status_pemesanan' => 'Menunggu Konfirmasi'
        ]);

        return response()->json([
            'message' => 'Bukti pembayaran berhasil diunggah',
            'bukti_pembayaran' => $uploadResult->getSecurePath(),
            'status_pembayaran' => 'Pending'
        ]);
    }
}