<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KendaraanSeeder extends Seeder
{
    public function run()
    {
        DB::table('kendaraan')->insert([
            [
                'gambar_url' => 'https://picsum.photos/200/300',
                'kategori_kendaraan' => 'Mobil',
                'merek_model' => 'Toyota Avanza',
                'kapasitas_kursi' => 7,
                'jenis_transmisi' => 'Automatic',
                'tahun_produksi' => 2020,
                'nomor_polisi' => 'B 1234 ABC',
                'status_ketersediaan' => 'Tersedia',
                'harga_sewa_per_periode' => 350000,
                'kondisi_fasilitas' => 'AC, Audio, GPS',
                'lokasi_kendaraan' => 'Jakarta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'gambar_url' => 'https://picsum.photos/200/300',
                'kategori_kendaraan' => 'Mobil',
                'merek_model' => 'Honda Jazz',
                'kapasitas_kursi' => 5,
                'jenis_transmisi' => 'Manual',
                'tahun_produksi' => 2018,
                'nomor_polisi' => 'D 5678 XYZ',
                'status_ketersediaan' => 'Disewa',
                'harga_sewa_per_periode' => 300000,
                'kondisi_fasilitas' => 'AC, Audio, Sunroof',
                'lokasi_kendaraan' => 'Bandung',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'gambar_url' => 'https://picsum.photos/200/300',
                'kategori_kendaraan' => 'Mobil',
                'merek_model' => 'Suzuki Ertiga',
                'kapasitas_kursi' => 7,
                'jenis_transmisi' => 'Automatic',
                'tahun_produksi' => 2019,
                'nomor_polisi' => 'A 9101 BCD',
                'status_ketersediaan' => 'Tersedia',
                'harga_sewa_per_periode' => 325000,
                'kondisi_fasilitas' => 'AC, Audio, Bluetooth',
                'lokasi_kendaraan' => 'Surabaya',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'gambar_url' => 'https://picsum.photos/200/300',
                'kategori_kendaraan' => 'Mobil',
                'merek_model' => 'Mitsubishi Pajero',
                'kapasitas_kursi' => 7,
                'jenis_transmisi' => 'Automatic',
                'tahun_produksi' => 2021,
                'nomor_polisi' => 'L 1122 DEF',
                'status_ketersediaan' => 'Tersedia',
                'harga_sewa_per_periode' => 500000,
                'kondisi_fasilitas' => 'AC, Audio, GPS, Kamera Mundur',
                'lokasi_kendaraan' => 'Malang',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'gambar_url' => 'https://picsum.photos/200/300',
                'kategori_kendaraan' => 'Mobil',
                'merek_model' => 'Daihatsu Xenia',
                'kapasitas_kursi' => 7,
                'jenis_transmisi' => 'Manual',
                'tahun_produksi' => 2017,
                'nomor_polisi' => 'F 3344 GHI',
                'status_ketersediaan' => 'Tersedia',
                'harga_sewa_per_periode' => 275000,
                'kondisi_fasilitas' => 'AC, Audio',
                'lokasi_kendaraan' => 'Yogyakarta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
