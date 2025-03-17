<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'nama_lengkap' => 'testing',
                'email' => 'testing@testing.com',
                'status_blokir' => 'Tidak',
                'password' => Hash::make('testing')
            ],
            [
                'nama_lengkap' => 'testing1',
                'email' => 'testing2@testing.com',
                'status_blokir' => 'Tidak',
                'password' => Hash::make('testing2')
            ]
        ]);
    }
}
