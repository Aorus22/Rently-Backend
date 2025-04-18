<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(LokasiGarasiSeeder::class);
        $this->call(KendaraanSeeder::class);
        $this->call(AdminSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(PemesananSeeder::class);
        $this->call(PelacakanSeeder::class);
        $this->call(PerawatanSeeder::class);
    }
}
