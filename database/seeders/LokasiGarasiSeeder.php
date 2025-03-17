<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LokasiGarasiSeeder extends Seeder
{
    public function run()
    {
        DB::table('lokasi_garasi')->insert([
            ['kota' => 'Jakarta', 'alamat' => 'Jl. Merdeka No. 1', 'latitude' => -6.2088, 'longitude' => 106.8456, 'created_at' => now(), 'updated_at' => now()],
            ['kota' => 'Bandung', 'alamat' => 'Jl. Asia Afrika No. 10', 'latitude' => -6.9175, 'longitude' => 107.6191, 'created_at' => now(), 'updated_at' => now()],
            ['kota' => 'Surabaya', 'alamat' => 'Jl. Pemuda No. 20', 'latitude' => -7.2504, 'longitude' => 112.7688, 'created_at' => now(), 'updated_at' => now()],
            ['kota' => 'Malang', 'alamat' => 'Jl. Ijen No. 5', 'latitude' => -7.9785, 'longitude' => 112.5617, 'created_at' => now(), 'updated_at' => now()],
            ['kota' => 'Yogyakarta', 'alamat' => 'Jl. Malioboro No. 3', 'latitude' => -7.7956, 'longitude' => 110.3695, 'created_at' => now(), 'updated_at' => now()],
            ['kota' => 'Semarang', 'alamat' => 'Jl. Pandanaran No. 8', 'latitude' => -6.9667, 'longitude' => 110.4167, 'created_at' => now(), 'updated_at' => now()],
            ['kota' => 'Denpasar', 'alamat' => 'Jl. Gatot Subroto No. 15', 'latitude' => -8.6705, 'longitude' => 115.2126, 'created_at' => now(), 'updated_at' => now()],
            ['kota' => 'Makassar', 'alamat' => 'Jl. Pettarani No. 25', 'latitude' => -5.1477, 'longitude' => 119.4327, 'created_at' => now(), 'updated_at' => now()],
            ['kota' => 'Bali', 'alamat' => 'Jl. Sunset Road No. 7', 'latitude' => -8.4095, 'longitude' => 115.1889, 'created_at' => now(), 'updated_at' => now()],
            ['kota' => 'Medan', 'alamat' => 'Jl. Gatot Subroto No. 12', 'latitude' => 3.5952, 'longitude' => 98.6722, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
