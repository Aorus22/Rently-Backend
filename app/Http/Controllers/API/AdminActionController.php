<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Pemesanan;
use App\Models\Pembayaran;
use App\Models\Kendaraan;
use App\Models\KontrakSewa;
use Illuminate\Support\Facades\DB;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class AdminActionController extends Controller
{
    // Mengonfirmasi pembayaran lunas
    public function confirmLunas(Request $request, $pembayaran_id)
    {
        $request->validate([
            'jumlah_pembayaran' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            $pembayaran = Pembayaran::findOrFail($pembayaran_id);
            $pembayaran->status_pembayaran = 'Lunas';
            $pembayaran->jumlah_pembayaran = $request->jumlah_pembayaran;
            $pembayaran->tanggal_pembayaran = now();
            $pembayaran->save();

            $pemesanan = Pemesanan::findOrFail($pembayaran->pemesanan_id);
            $pemesanan->status_pemesanan = 'Dikonfirmasi';
            $pemesanan->save();

            $kendaraan = Kendaraan::findOrFail($pemesanan->kendaraan_id);
            $kendaraan->status_ketersediaan = 'Disewa';
            $kendaraan->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dikonfirmasi',
                'data' => [
                    'pembayaran' => $pembayaran,
                    'pemesanan' => $pemesanan,
                    'kendaraan' => $kendaraan
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengonfirmasi pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    // Mengonfirmasi pembayaran belum lunas
    public function confirmBelumLunas(Request $request, $pembayaran_id)
    {
        $request->validate([
            'jumlah_pembayaran' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            $pembayaran = Pembayaran::findOrFail($pembayaran_id);
            $pembayaran->status_pembayaran = 'Belum Dibayar';
            $pembayaran->jumlah_pembayaran = $request->jumlah_pembayaran;
            $pembayaran->tanggal_pembayaran = null;
            $pembayaran->save();

            $pemesanan = Pemesanan::findOrFail($pembayaran->pemesanan_id);
            $pemesanan->status_pemesanan = 'Menunggu Pembayaran';
            $pemesanan->save();

            $kendaraan = Kendaraan::findOrFail($pemesanan->kendaraan_id);
            $kendaraan->status_ketersediaan = 'Tersedia';
            $kendaraan->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil diperbarui',
                'data' => [
                    'pembayaran' => $pembayaran,
                    'pemesanan' => $pemesanan,
                    'kendaraan' => $kendaraan
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan konfirmasi pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    // Mengambil semua pemesanan beserta detail pembayarannya
    public function getAllPemesanan()
    {
        try {
            $pemesanan = Pemesanan::with([
                'pembayaran',
                'kendaraan',
                'user'
            ])->get();

            if ($pemesanan->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tidak ada pemesanan ditemukan.',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'success' => true,
                'message' => 'Daftar pemesanan berhasil diambil.',
                'data' => $pemesanan
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pemesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Mengambil detail pemesanan berdasarkan ID
    public function getPemesananDetail($id)
    {
        try {
            $pemesanan = Pemesanan::with([
                'pembayaran',
                'kendaraan',
                'user',
                'kontrakSewa'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail pemesanan berhasil diambil.',
                'data' => $pemesanan
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail pemesanan: ' . $e->getMessage()
            ], 404);
        }
    }

    // Upload kontrak sewa
    public function uploadKontrakSewa(Request $request, $pemesanan_id)
    {
        $request->validate([
            'file_kontrak' => 'required|file|mimes:pdf|max:10000'
        ]);

        try {
            DB::beginTransaction();

            $pemesanan = Pemesanan::findOrFail($pemesanan_id);

            $uploadedFile = $request->file('file_kontrak');
            $uploadResult = Cloudinary::upload($uploadedFile->getRealPath(), [
                'folder' => 'kontrak_sewa',
                'resource_type' => 'auto'
            ]);
            $kontrakUrl = $uploadResult->getSecurePath();

            $kontrakSewa = new KontrakSewa();
            $kontrakSewa->pemesanan_id = $pemesanan_id;
            $kontrakSewa->link_kontrak = $kontrakUrl;
            $kontrakSewa->status_kontrak = 'Aktif';
            $kontrakSewa->save();

            $pemesanan->status_pemesanan = 'Sedang dalam Penggunaan';
            $pemesanan->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kontrak sewa berhasil diunggah dan status pemesanan diperbarui menjadi Sedang dalam Penggunaan.',
                'data' => [
                    'kontrak_sewa' => $kontrakSewa,
                    'pemesanan' => $pemesanan
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah kontrak sewa: ' . $e->getMessage()
            ], 500);
        }
    }

    public function confirmPengembalian($pemesanan_id)
    {
        try {
            DB::beginTransaction();

            $pemesanan = Pemesanan::findOrFail($pemesanan_id);
            $pemesanan->status_pemesanan = 'Selesai';
            $pemesanan->save();

            $kendaraan = Kendaraan::findOrFail($pemesanan->kendaraan_id);
            $kendaraan->status_ketersediaan = 'Tersedia';
            $kendaraan->save();

            $kontrakSewa = KontrakSewa::where('pemesanan_id', $pemesanan_id)->first();
            if ($kontrakSewa) {
                $kontrakSewa->status_kontrak = 'Selesai';
                $kontrakSewa->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengembalian kendaraan berhasil dikonfirmasi.',
                'data' => $pemesanan
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengonfirmasi pengembalian: ' . $e->getMessage()
            ], 500);
        }
    }
}
