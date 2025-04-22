<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\PerawatanKendaraan;
use Illuminate\Support\Facades\Hash;

class PerawatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 100; $i++) {
            PerawatanKendaraan::create([
                'kendaraan_id' => 4,
                'tanggal_perawatan' => Carbon::now()->subDays(rand(0, 365)),
                'jenis_perawatan' => fake()->randomElement(['Ganti Oli', 'Servis Mesin', 'Cuci Mobil', 'Pengecekan Rem']),
                'biaya_perawatan' => fake()->randomFloat(2, 100_000, 1_000_000),
                'bengkel_teknisi' => fake()->company(),
                'catatan_tambahan' => fake()->optional()->sentence(),
            ]);
        }
    }
}
