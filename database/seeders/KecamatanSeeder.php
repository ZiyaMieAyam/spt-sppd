<?php

namespace Database\Seeders;

use App\Models\Kecamatan;
use Illuminate\Database\Seeder;

class KecamatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kecamatans = [
            ['nama' => 'Awayan'],
            ['nama' => 'Batu Mandi'],
            ['nama' => 'Halong'],
            ['nama' => 'Juai'],
            ['nama' => 'Lampihong'],
            ['nama' => 'Paringin'],
            ['nama' => 'Paringin Selatan'],
            ['nama' => 'Tebing Tinggi'],
        ];

        foreach ($kecamatans as $kecamatan) {
            Kecamatan::firstOrCreate($kecamatan);
        }
    }
}