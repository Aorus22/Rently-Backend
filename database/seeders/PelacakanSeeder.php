<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PelacakanKendaraan;
use Illuminate\Support\Facades\Hash;

class PelacakanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 100; $i++) {
            PelacakanKendaraan::create([
                'kendaraan_id' => 4,
                'lokasi_mobil_digunakan' => 'Lokasi ke-' . $i,
                'status_kondisi_setelah_sewa' => 'Kondisi ke-' . $i,
            ]);
        }
    }
}
