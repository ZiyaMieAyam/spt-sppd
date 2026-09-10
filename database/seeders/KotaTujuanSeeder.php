<?php

namespace Database\Seeders;

use App\Models\KotaTujuan;
use Illuminate\Database\Seeder;

class KotaTujuanSeeder extends Seeder
{
    public function run(): void
    {
        // 99 Kota PERSIS dari prompt — jangan ubah, jangan cari sumber lain
        $kotas = [
            // ACEH 5
            'Banda Aceh',
            'Langsa',
            'Lhokseumawe',
            'Sabang',
            'Subulussalam',
            // BALI 1
            'Denpasar',
            // BANGKA BELITUNG 1
            'Pangkalpinang',
            // BANTEN 4
            'Cilegon',
            'Serang',
            'Tangerang Selatan',
            'Tangerang',
            // BENGKULU 1
            'Bengkulu',
            // DIY 1
            'Yogyakarta',
            // DKI JAKARTA 5
            'Kota Administrasi Jakarta Barat',
            'Kota Administrasi Jakarta Pusat',
            'Kota Administrasi Jakarta Selatan',
            'Kota Administrasi Jakarta Timur',
            'Kota Administrasi Jakarta Utara',
            // GORONTALO 1
            'Gorontalo',
            // JAMBI 2
            'Jambi',
            'Sungai Penuh',
            // JAWA BARAT 9
            'Bandung',
            'Bekasi',
            'Bogor',
            'Cimahi',
            'Cirebon',
            'Depok',
            'Sukabumi',
            'Tasikmalaya',
            'Banjar',
            // JAWA TENGAH 6
            'Magelang',
            'Pekalongan',
            'Salatiga',
            'Semarang',
            'Surakarta',
            'Tegal',
            // JAWA TIMUR 9
            'Batu',
            'Blitar',
            'Kediri',
            'Madiun',
            'Malang',
            'Mojokerto',
            'Pasuruan',
            'Probolinggo',
            'Surabaya',
            // KALIMANTAN BARAT 2
            'Pontianak',
            'Singkawang',
            // KALIMANTAN SELATAN 2
            'Banjarbaru',
            'Banjarmasin',
            // KALIMANTAN TENGAH 1
            'Palangka Raya',
            // KALIMANTAN TIMUR 3
            'Balikpapan',
            'Bontang',
            'Samarinda',
            // NUSANTARA 1
            'Nusantara',
            // KALIMANTAN UTARA 1
            'Tarakan',
            // KEPULAUAN RIAU 2
            'Batam',
            'Tanjungpinang',
            // LAMPUNG 2
            'Bandar Lampung',
            'Metro',
            // MALUKU UTARA 2
            'Ternate',
            'Tidore Kepulauan',
            // MALUKU 2
            'Ambon',
            'Tual',
            // NTB 2
            'Bima',
            'Mataram',
            // NTT 1
            'Kupang',
            // PAPUA BARAT DAYA 1
            'Sorong',
            // PAPUA 1
            'Jayapura',
            // RIAU 2
            'Dumai',
            'Pekanbaru',
            // SULSEL 3
            'Makassar',
            'Palopo',
            'Parepare',
            // SULTENG 1
            'Palu',
            // SULTRA 2
            'Baubau',
            'Kendari',
            // SULUT 4
            'Bitung',
            'Kotamobagu',
            'Manado',
            'Tomohon',
            // SUMBAR 7
            'Bukittinggi',
            'Padang',
            'Padang Panjang',
            'Pariaman',
            'Payakumbuh',
            'Sawahlunto',
            'Solok',
            // SUMSEL 4
            'Lubuk Linggau',
            'Pagar Alam',
            'Palembang',
            'Prabumulih',
            // SUMUT 8
            'Binjai',
            'Gunungsitoli',
            'Medan',
            'Padangsidimpuan',
            'Pematangsiantar',
            'Sibolga',
            'Tanjungbalai',
            'Tebing Tinggi',
        ];

        foreach ($kotas as $nama) {
            KotaTujuan::updateOrCreate(
                ['nama' => $nama],
                []
            );
        }
    }
}
