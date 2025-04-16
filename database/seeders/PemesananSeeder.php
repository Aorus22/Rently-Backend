<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pemesanan;
use Illuminate\Support\Carbon;

class PemesananSeeder extends Seeder
{
    public function run(): void
    {
        // Assuming you have users with IDs 1–5 and kendaraan with IDs 1–5
        foreach (range(1, 100) as $i) {
            $tanggalMulai = Carbon::now()->subDays(rand(1, 60));
            $tanggalSelesai = (clone $tanggalMulai)->addDays(rand(1, 7));
            $statusOptions = [
                'Menunggu Pembayaran',
                'Menunggu Konfirmasi',
                'Dikonfirmasi',
                'Sedang dalam Penggunaan',
                'Dibatalkan',
                'Selesai'
            ];

            Pemesanan::create([
                'user_id' => 1,
                'kendaraan_id' => 4,
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
                'total_harga_sewa' => rand(500_000, 3_000_000),
                'status_pemesanan' => $statusOptions[array_rand($statusOptions)],
            ]);
        }
    }
}

