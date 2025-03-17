<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Models\Kendaraan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PemesananController extends Controller
{
    // Ambil semua pemesanan untuk admin/user
    public function index()
    {
        $user = Auth::user();

        $pemesanan = Pemesanan::where('user_id', $user->id)
            ->with(['kendaraan.lokasiGarasi'])
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

        if ($kendaraan->status_ketersediaan !== 'Tersedia') {
            return response()->json(['message' => 'Kendaraan tidak tersedia untuk disewa'], 400);
        }

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
        $pemesanan = Pemesanan::with(['kendaraan.lokasiGarasi'])->findOrFail($id);

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

        $request->validate([
            'metode_pembayaran' => 'required|in:Transfer Bank,Kartu Kredit,E-Wallet',
            'jumlah_pembayaran' => 'required|numeric|min:0',
            'deposit_keamanan' => 'required|numeric|min:0',
            'bukti_pembayaran' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Simpan bukti pembayaran jika ada
        $path = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran');
        }

        // Hitung total pembayaran (harga sewa + deposit)
        $totalHarusDibayar = $pemesanan->total_harga_sewa + $request->deposit_keamanan;
        $statusPembayaran = ($request->jumlah_pembayaran >= $totalHarusDibayar) ? 'Lunas' : 'Belum Lunas';

        // Simpan ke tabel pembayaran
        $pembayaran = new Pembayaran([
            'pemesanan_id' => $pemesanan->id,
            'metode_pembayaran' => $request->metode_pembayaran,
            'jumlah_pembayaran' => $request->jumlah_pembayaran,
            'tanggal_pembayaran' => now(),
            'status_pembayaran' => $statusPembayaran,
            'deposit_keamanan' => $request->deposit_keamanan,
            'bukti_pembayaran' => $path,
        ]);
        $pembayaran->save();

        $kendaraan = Kendaraan::find($pemesanan->kendaraan_id);
        $kendaraan->update(['status_ketersediaan' => 'Disewa']);

        // Update status pemesanan jika lunas
        if ($statusPembayaran === 'Lunas') {
            $pemesanan->update(['status_pemesanan' => 'Dikonfirmasi']);
        } else {
            $pemesanan->update(['status_pemesanan' => 'Menunggu Konfirmasi']);
        }

        return response()->json(['message' => 'Pembayaran berhasil dikonfirmasi', 'status_pembayaran' => $statusPembayaran]);
    }

    public function getPembayaranByPemesanan($id)
    {
        $user = Auth::user();
        $pemesanan = Pemesanan::findOrFail($id);

        if ($pemesanan->user_id !== $user->id && !$user->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $pembayaran = $pemesanan->pembayaran;
        return response()->json($pembayaran);
    }

    public function getAllPemesanan() {
        $pemesanan = Pemesanan::with([
            'kendaraan:id,merek_model',
            'user:id,nama_lengkap'
        ])->get();

        return response()->json($pemesanan);
    }

    public function updatePemesanan(Request $request, $id)
    {
        $Pemesanan = Pemesanan::find($id);
        if (!$Pemesanan) {
            return response()->json(['message' => 'Pemesanan tidak ditemukan'], 404);
        }

        $data = $request->only(['status_pemesanan']);
        $Pemesanan->update($data);
        return response()->json(['message' => 'Pemesanan berhasil diperbarui!', 'data' => $Pemesanan]);
    }
}
