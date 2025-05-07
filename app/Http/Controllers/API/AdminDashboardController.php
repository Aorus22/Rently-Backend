<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kendaraan;
use App\Models\Pemesanan;
use App\Models\User;
use App\Models\Pembayaran;
use App\Models\LokasiGarasi;
use App\Models\PerawatanKendaraan;
use App\Models\KontrakSewa;
use App\Models\PelacakanKendaraan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $tahunIni = Carbon::now()->year;
        $bulanIni = Carbon::now()->month;
        $hariIni = Carbon::today();
        $mingguLalu = Carbon::now()->subDays(7);
        $tigaBulanLalu = Carbon::now()->subMonths(3);

        $dashboardData = [
            // ===== STATISTIK UMUM =====
            'statistik_umum' => [
                'total_kendaraan' => Kendaraan::count(),
                'total_user' => User::count(),
                'total_pemesanan' => Pemesanan::count(),
                'total_garasi' => LokasiGarasi::count(),
                'user_baru_bulan_ini' => User::whereYear('created_at', $tahunIni)
                    ->whereMonth('created_at', $bulanIni)
                    ->count(),
                'user_terblokir' => User::where('status_blokir', 'Ya')->count(),
            ],

            // ===== STATUS KENDARAAN =====
            'status_kendaraan' => [
                'tersedia' => Kendaraan::where('status_ketersediaan', 'Tersedia')->count(),
                'disewa' => Kendaraan::where('status_ketersediaan', 'Disewa')->count(),
                'perawatan' => Kendaraan::where('status_ketersediaan', 'Perawatan')->count(),
            ],

            // ===== KENDARAAN PER KATEGORI =====
            'kendaraan_per_kategori' => [
                'mobil' => Kendaraan::where('kategori_kendaraan', 'Mobil')->count(),
                'minibus' => Kendaraan::where('kategori_kendaraan', 'Minibus')->count(),
                'pickup' => Kendaraan::where('kategori_kendaraan', 'Pickup')->count(),
            ],

            // ===== STATISTIK PEMESANAN =====
            'statistik_pemesanan' => [
                'menunggu_pembayaran' => Pemesanan::where('status_pemesanan', 'Menunggu Pembayaran')->count(),
                'menunggu_konfirmasi' => Pemesanan::where('status_pemesanan', 'Menunggu Konfirmasi')->count(),
                'dikonfirmasi' => Pemesanan::where('status_pemesanan', 'Dikonfirmasi')->count(),
                'sedang_digunakan' => Pemesanan::where('status_pemesanan', 'Sedang dalam Penggunaan')->count(),
                'selesai' => Pemesanan::where('status_pemesanan', 'Selesai')->count(),
                'dibatalkan' => Pemesanan::where('status_pemesanan', 'Dibatalkan')->count(),
                'pemesanan_minggu_ini' => Pemesanan::where('created_at', '>=', $mingguLalu)->count(),
            ],

            // ===== STATISTIK KEUANGAN =====
            'statistik_keuangan' => [
                'pendapatan_hari_ini' => Pembayaran::whereDate('tanggal_pembayaran', $hariIni)
                    ->where('status_pembayaran', 'Lunas')
                    ->sum('jumlah_pembayaran'),

                'pendapatan_minggu_ini' => Pembayaran::where('tanggal_pembayaran', '>=', $mingguLalu)
                    ->where('status_pembayaran', 'Lunas')
                    ->sum('jumlah_pembayaran'),

                'pendapatan_bulan_ini' => Pembayaran::whereYear('tanggal_pembayaran', $tahunIni)
                    ->whereMonth('tanggal_pembayaran', $bulanIni)
                    ->where('status_pembayaran', 'Lunas')
                    ->sum('jumlah_pembayaran'),

                'pendapatan_tahun_ini' => Pembayaran::whereYear('tanggal_pembayaran', $tahunIni)
                    ->where('status_pembayaran', 'Lunas')
                    ->sum('jumlah_pembayaran'),

                'pembayaran_pending' => Pembayaran::where('status_pembayaran', 'Pending')->count(),
                'deposit_belum_kembali' => Pembayaran::whereHas('pemesanan', function($query) {
                        $query->where('status_pemesanan', 'Selesai');
                    })
                    ->sum('deposit_keamanan'),
            ],

            // ===== GRAFIK PENDAPATAN BULANAN =====
            'grafik_pendapatan_bulanan' => $this->getPendapatanBulanan($tahunIni),

            // ===== GRAFIK PEMESANAN 7 HARI TERAKHIR =====
            'grafik_pemesanan_mingguan' => $this->getPemesananMingguan(),

            // ===== PEMESANAN TERBARU =====
            'pemesanan_terbaru' => Pemesanan::with(['user', 'kendaraan'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),

            // ===== PEMBAYARAN TERBARU =====
            'pembayaran_terbaru' => Pembayaran::with(['pemesanan', 'pemesanan.user', 'pemesanan.kendaraan'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),

            // ===== KENDARAAN POPULER =====
            'kendaraan_populer' => $this->getKendaraanPopuler(),

            // ===== KENDARAAN PERLU PERAWATAN =====
            'kendaraan_perlu_perawatan' => $this->getKendaraanPerluPerawatan($tigaBulanLalu),

            // ===== PEMESANAN PERLU KONFIRMASI =====
            'pemesanan_perlu_konfirmasi' => Pemesanan::with(['user', 'kendaraan'])
                ->where('status_pemesanan', 'Menunggu Konfirmasi')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),

            // ===== LOKASI GARASI POPULAR =====
            'lokasi_garasi_populer' => $this->getLokasiGarasiPopuler(),

            // ===== USER PALING AKTIF =====
            'user_paling_aktif' => $this->getUserPalingAktif(),

            // ===== PERFORMA KATEGORI KENDARAAN =====
            'performa_kategori' => $this->getPerformaKategori(),

            // ===== KONTRAK SEWA AKTIF =====
            'kontrak_sewa_aktif' => KontrakSewa::where('status_kontrak', 'Aktif')->count(),

            // ===== ESTIMASI PENDAPATAN BULAN INI =====
            'estimasi_pendapatan_bulan_ini' => Pemesanan::whereYear('tanggal_mulai', $tahunIni)
                ->whereMonth('tanggal_mulai', $bulanIni)
                ->where('status_pemesanan', '!=', 'Dibatalkan')
                ->sum('total_harga_sewa'),
        ];

        return response()->json([
            'success' => true,
            'data' => $dashboardData
        ]);
    }

    //  Mendapatkan data pendapatan bulanan untuk tahun tertentu
    private function getPendapatanBulanan($tahun)
    {
        $pendapatanPerBulan = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $pendapatan = Pembayaran::whereYear('tanggal_pembayaran', $tahun)
                ->whereMonth('tanggal_pembayaran', $bulan)
                ->where('status_pembayaran', 'Lunas')
                ->sum('jumlah_pembayaran');

            $pendapatanPerBulan[] = [
                'bulan' => Carbon::create()->month($bulan)->format('F'),
                'pendapatan' => $pendapatan
            ];
        }

        return $pendapatanPerBulan;
    }

    //  Mendapatkan data pemesanan 7 hari terakhir
    private function getPemesananMingguan()
    {
        $pemesananMingguIni = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = Pemesanan::whereDate('created_at', $date->toDateString())->count();
            $pemesananMingguIni[] = [
                'tanggal' => $date->format('d M'),
                'jumlah' => $count
            ];
        }

        return $pemesananMingguIni;
    }


     // Mendapatkan kendaraan populer berdasarkan jumlah pemesanan
    private function getKendaraanPopuler()
    {
        // Mencari ID kendaraan yang paling banyak dipesan
        $kendaraanPopulerIds = Pemesanan::select('kendaraan_id')
            ->selectRaw('COUNT(*) as jumlah_pemesanan')
            ->groupBy('kendaraan_id')
            ->orderBy('jumlah_pemesanan', 'desc')
            ->take(5)
            ->get();

        // Mengambil detail kendaraan populer
        $kendaraanPopuler = [];
        foreach ($kendaraanPopulerIds as $item) {
            $kendaraan = Kendaraan::find($item->kendaraan_id);
            if ($kendaraan) {
                $kendaraanPopuler[] = [
                    'id' => $kendaraan->id,
                    'merek_model' => $kendaraan->merek_model,
                    'nomor_polisi' => $kendaraan->nomor_polisi,
                    'kategori_kendaraan' => $kendaraan->kategori_kendaraan,
                    'jumlah_pemesanan' => $item->jumlah_pemesanan
                ];
            }
        }

        return $kendaraanPopuler;
    }


     // Mendapatkan daftar kendaraan yang memerlukan perawatan
    private function getKendaraanPerluPerawatan($tigaBulanLalu)
    {
        // Dapatkan semua kendaraan
        $semuaKendaraan = Kendaraan::with('lokasiGarasi')->get();
        $kendaraanPerluPerawatan = [];

        foreach ($semuaKendaraan as $kendaraan) {
            // Cari perawatan terakhir untuk kendaraan ini
            $perawatanTerakhir = PerawatanKendaraan::where('kendaraan_id', $kendaraan->id)
                ->orderBy('tanggal_perawatan', 'desc')
                ->first();

            // Jika tidak ada perawatan atau perawatan terakhir > 3 bulan
            if (!$perawatanTerakhir || Carbon::parse($perawatanTerakhir->tanggal_perawatan)->lt($tigaBulanLalu)) {
                $kendaraanPerluPerawatan[] = $kendaraan;
            }
        }

        return $kendaraanPerluPerawatan;
    }


     // Mendapatkan lokasi garasi populer berdasarkan jumlah kendaraan
    private function getLokasiGarasiPopuler()
    {
        $lokasiGarasi = LokasiGarasi::all();

        $hasil = [];
        foreach ($lokasiGarasi as $garasi) {
            $jumlahKendaraan = Kendaraan::where('lokasi_garasi_id', $garasi->id)->count();

            $hasil[] = [
                'id' => $garasi->id,
                'kota' => $garasi->kota,
                'alamat' => $garasi->alamat,
                'jumlah_kendaraan' => $jumlahKendaraan
            ];
        }

        // Urutkan berdasarkan jumlah kendaraan terbanyak
        usort($hasil, function($a, $b) {
            return $b['jumlah_kendaraan'] <=> $a['jumlah_kendaraan'];
        });

        return $hasil;
    }


    //  Mendapatkan user paling aktif berdasarkan jumlah pemesanan
    private function getUserPalingAktif()
    {
        // Mencari ID user yang paling banyak melakukan pemesanan
        $userAktifIds = Pemesanan::select('user_id')
            ->selectRaw('COUNT(*) as jumlah_pemesanan')
            ->groupBy('user_id')
            ->orderBy('jumlah_pemesanan', 'desc')
            ->take(5)
            ->get();

        // Mengambil detail user aktif
        $userPalingAktif = [];
        foreach ($userAktifIds as $item) {
            $user = User::find($item->user_id);
            if ($user) {
                $userPalingAktif[] = [
                    'id' => $user->id,
                    'nama_lengkap' => $user->nama_lengkap,
                    'email' => $user->email,
                    'jumlah_pemesanan' => $item->jumlah_pemesanan
                ];
            }
        }

        return $userPalingAktif;
    }

    // Mendapatkan performa kategori kendaraan berdasarkan jumlah pemesanan
    private function getPerformaKategori()
    {
        $kategoriList = ['Mobil', 'Minibus', 'Pickup'];
        $hasil = [];

        foreach ($kategoriList as $kategori) {
            $jumlahPemesanan = Pemesanan::whereHas('kendaraan', function (Builder $query) use ($kategori) {
                $query->where('kategori_kendaraan', $kategori);
            })->count();

            $hasil[] = [
                'kategori' => $kategori,
                'jumlah_pemesanan' => $jumlahPemesanan
            ];
        }

        // Urutkan berdasarkan jumlah pemesanan terbanyak
        usort($hasil, function($a, $b) {
            return $b['jumlah_pemesanan'] <=> $a['jumlah_pemesanan'];
        });

        return $hasil;
    }
}
