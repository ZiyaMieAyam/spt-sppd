<?php

namespace Database\Seeders;

use App\Models\KotaTujuan;
use Illuminate\Database\Seeder;

class KotaTujuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kotas = [
            ['nama' => 'Banjarmasin'],
            ['nama' => 'Banjarbaru'],
            ['nama' => 'Balangan'],
            ['nama' => 'Tabalong'],
            ['nama' => 'Hulu Sungai Utara'],
            ['nama' => 'Hulu Sungai Tengah'],
            ['nama' => 'Hulu Sungai Selatan'],
            ['nama' => 'Tanah Laut'],
            ['nama' => 'Tanah Bumbu'],
            ['nama' => 'Kotabaru'],
            ['nama' => 'Barito Kuala'],
            ['nama' => 'Tapin'],
            ['nama' => 'Banjar'],
            ['nama' => 'Palangka Raya'],
            ['nama' => 'Samarinda'],
            ['nama' => 'Balikpapan'],
            ['nama' => 'Pontianak'],
            ['nama' => 'Jakarta'],
            ['nama' => 'Bandung'],
            ['nama' => 'Yogyakarta'],
            ['nama' => 'Semarang'],
            ['nama' => 'Surabaya'],
            ['nama' => 'Malang'],
            ['nama' => 'Denpasar'],
            ['nama' => 'Mataram'],
            ['nama' => 'Makassar'],
            ['nama' => 'Manado'],
            ['nama' => 'Palu'],
            ['nama' => 'Kendari'],
            ['nama' => 'Medan'],
            ['nama' => 'Padang'],
            ['nama' => 'Pekanbaru'],
            ['nama' => 'Batam'],
            ['nama' => 'Palembang'],
            ['nama' => 'Lampung'],
            ['nama' => 'Jambi'],
            ['nama' => 'Bengkulu'],
            ['nama' => 'Aceh'],
            ['nama' => 'Sorong'],
            ['nama' => 'Jayapura'],
        ];

        foreach ($kotas as $kota) {
            KotaTujuan::firstOrCreate($kota);
        }
    }
}