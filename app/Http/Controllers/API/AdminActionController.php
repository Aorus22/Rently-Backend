<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Pemesanan;
use App\Models\Pembayaran;
use App\Models\Kendaraan;
use Illuminate\Support\Facades\DB;

class AdminActionController extends Controller
{
    // Fungsi untuk mengonfirmasi pembayaran
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
                'message' => 'Pembayaran berhasil dikonfirmasi, jumlah pembayaran diperbarui, status pemesanan menjadi Dikonfirmasi, dan kendaraan ditandai sebagai Disewa.',
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
                'message' => 'Pembayaran berhasil dibatalkan, jumlah pembayaran diperbarui, status pemesanan menjadi Menunggu Pembayaran, dan kendaraan kembali Tersedia.',
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

    // Fungsi untuk mengambil semua pemesanan beserta detail pembayarannya
    public function getAllPemesanan()
    {
        try {
            $pemesanan = Pemesanan::with([
                'pembayaran',
                'kendaraan',
                'user'
            ])->get();

            // Jika tidak ada pemesanan
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

    public function getPemesananDetail($id)
    {
        try {
            $pemesanan = Pemesanan::with([
                'pembayaran',
                'kendaraan',
                'user'
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
}