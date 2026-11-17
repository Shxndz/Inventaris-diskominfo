<?php

namespace Database\Seeders;

use App\Models\Sekbid;
use Illuminate\Database\Seeder;

class SekbidSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $daftar = [
            'Sekretariat',
            'Bidang Informasi dan Komunikasi Publik',
            'Bidang Aplikasi dan Informatika',
            'Bidang Persandian dan Keamanan Informasi',
            'Bidang Statistik dan Data',
        ];

        $this->call([
            SekbidSeeder::class,
            AdminSeeder::class,
        ]);

        foreach ($daftar as $nama) {
            Sekbid::firstOrCreate(['nama' => $nama]);
        }
    }
}