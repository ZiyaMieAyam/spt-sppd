<?php

namespace Database\Seeders;

use App\Models\Desa;
use App\Models\Kecamatan;
use Illuminate\Database\Seeder;

class KecamatanSeeder extends Seeder
{
    public function run(): void
    {
        // Data persis dari prompt: 8 kecamatan, 154 desa, 3 kelurahan = 157
        $data = [
            'Awayan' => [
                // 23 desa
                ['nama' => 'Ambakiang', 'tipe' => 'desa'],
                ['nama' => 'Awayan', 'tipe' => 'desa'],
                ['nama' => 'Awayan Hilir', 'tipe' => 'desa'],
                ['nama' => 'Badalungga', 'tipe' => 'desa'],
                ['nama' => 'Badalungga Hilir', 'tipe' => 'desa'],
                ['nama' => 'Baramban', 'tipe' => 'desa'],
                ['nama' => 'Baru', 'tipe' => 'desa'],
                ['nama' => 'Bihara', 'tipe' => 'desa'],
                ['nama' => 'Bihara Hilir', 'tipe' => 'desa'],
                ['nama' => 'Kedondong', 'tipe' => 'desa'],
                ['nama' => 'Merah', 'tipe' => 'desa'],
                ['nama' => 'Muara Jaya', 'tipe' => 'desa'],
                ['nama' => 'Nungka', 'tipe' => 'desa'],
                ['nama' => 'Pematang', 'tipe' => 'desa'],
                ['nama' => 'Piyait', 'tipe' => 'desa'],
                ['nama' => 'Pudak', 'tipe' => 'desa'],
                ['nama' => 'Pulantan', 'tipe' => 'desa'],
                ['nama' => 'Putat Basiun', 'tipe' => 'desa'],
                ['nama' => 'Sikontan', 'tipe' => 'desa'],
                ['nama' => 'Sungai Pumpung', 'tipe' => 'desa'],
                ['nama' => 'Tangalin', 'tipe' => 'desa'],
                ['nama' => 'Tundakan', 'tipe' => 'desa'],
                ['nama' => 'Tundi', 'tipe' => 'desa'],
            ],
            'Batu Mandi' => [
                // 18 desa
                ['nama' => 'Bakung', 'tipe' => 'desa'],
                ['nama' => 'Banua Hanyar', 'tipe' => 'desa'],
                ['nama' => 'Batu Mandi', 'tipe' => 'desa'],
                ['nama' => 'Bungur', 'tipe' => 'desa'],
                ['nama' => 'Guha', 'tipe' => 'desa'],
                ['nama' => 'Gunung Manau', 'tipe' => 'desa'],
                ['nama' => 'Hamparaya', 'tipe' => 'desa'],
                ['nama' => 'Karuh', 'tipe' => 'desa'],
                ['nama' => 'Kasai', 'tipe' => 'desa'],
                ['nama' => 'Lok Batu', 'tipe' => 'desa'],
                ['nama' => 'Mampari', 'tipe' => 'desa'],
                ['nama' => 'Mantimin', 'tipe' => 'desa'],
                ['nama' => 'Munjung', 'tipe' => 'desa'],
                ['nama' => 'Pelajau', 'tipe' => 'desa'],
                ['nama' => 'Riwa', 'tipe' => 'desa'],
                ['nama' => 'Tariwin', 'tipe' => 'desa'],
                ['nama' => 'Teluk Mesjid', 'tipe' => 'desa'],
                ['nama' => 'Timbun Tulang', 'tipe' => 'desa'],
            ],
            'Halong' => [
                // 24 desa
                ['nama' => 'Aniungan', 'tipe' => 'desa'],
                ['nama' => 'Bangkal', 'tipe' => 'desa'],
                ['nama' => 'Baruh Panyambaran', 'tipe' => 'desa'],
                ['nama' => 'Binjai Punggal', 'tipe' => 'desa'],
                ['nama' => 'Binju', 'tipe' => 'desa'],
                ['nama' => 'Binuang Santang', 'tipe' => 'desa'],
                ['nama' => 'Buntu Pilanduk', 'tipe' => 'desa'],
                ['nama' => 'Gunung Riut', 'tipe' => 'desa'],
                ['nama' => 'Halong', 'tipe' => 'desa'],
                ['nama' => 'Hauwai', 'tipe' => 'desa'],
                ['nama' => 'Kapul', 'tipe' => 'desa'],
                ['nama' => 'Karya', 'tipe' => 'desa'],
                ['nama' => 'Liyu', 'tipe' => 'desa'],
                ['nama' => 'Mamantang', 'tipe' => 'desa'],
                ['nama' => 'Mamigang', 'tipe' => 'desa'],
                ['nama' => 'Mantuyan', 'tipe' => 'desa'],
                ['nama' => 'Marajai', 'tipe' => 'desa'],
                ['nama' => 'Mauya', 'tipe' => 'desa'],
                ['nama' => 'Padang Raya', 'tipe' => 'desa'],
                ['nama' => 'Puyun', 'tipe' => 'desa'],
                ['nama' => 'Sumber Agung', 'tipe' => 'desa'],
                ['nama' => 'Suryatama', 'tipe' => 'desa'],
                ['nama' => 'Tabuan', 'tipe' => 'desa'],
                ['nama' => 'Uren', 'tipe' => 'desa'],
            ],
            'Juai' => [
                // 21 desa
                ['nama' => 'Bata', 'tipe' => 'desa'],
                ['nama' => 'Buntu Karau', 'tipe' => 'desa'],
                ['nama' => 'Galumbang', 'tipe' => 'desa'],
                ['nama' => 'Gulinggang', 'tipe' => 'desa'],
                ['nama' => 'Hamarung', 'tipe' => 'desa'],
                ['nama' => 'Hukai', 'tipe' => 'desa'],
                ['nama' => 'Juai', 'tipe' => 'desa'],
                ['nama' => 'Lalayau', 'tipe' => 'desa'],
                ['nama' => 'Marias', 'tipe' => 'desa'],
                ['nama' => 'Mihu', 'tipe' => 'desa'],
                ['nama' => 'Muara Ninian', 'tipe' => 'desa'],
                ['nama' => 'Mungkur Uyam', 'tipe' => 'desa'],
                ['nama' => 'Pamurus', 'tipe' => 'desa'],
                ['nama' => 'Panimbaan', 'tipe' => 'desa'],
                ['nama' => 'Sirap', 'tipe' => 'desa'],
                ['nama' => 'Sumber Rejeki', 'tipe' => 'desa'],
                ['nama' => 'Sumber Batung', 'tipe' => 'desa'],
                ['nama' => 'Tawahan', 'tipe' => 'desa'],
                ['nama' => 'Teluk Bayur', 'tipe' => 'desa'],
                ['nama' => 'Tigarun', 'tipe' => 'desa'],
                ['nama' => 'Wonorejo', 'tipe' => 'desa'],
            ],
            'Lampihong' => [
                // 27 desa
                ['nama' => 'Batu Merah', 'tipe' => 'desa'],
                ['nama' => 'Hilir Pasar', 'tipe' => 'desa'],
                ['nama' => 'Jimamun', 'tipe' => 'desa'],
                ['nama' => 'Jungkal', 'tipe' => 'desa'],
                ['nama' => 'Kandang Jaya', 'tipe' => 'desa'],
                ['nama' => 'Kupang', 'tipe' => 'desa'],
                ['nama' => 'Kusambi Hilir', 'tipe' => 'desa'],
                ['nama' => 'Kusambi Hulu', 'tipe' => 'desa'],
                ['nama' => 'Lajar', 'tipe' => 'desa'],
                ['nama' => 'Lampihong Kanan', 'tipe' => 'desa'],
                ['nama' => 'Lampihong Kiri', 'tipe' => 'desa'],
                ['nama' => 'Lampihong Selatan', 'tipe' => 'desa'],
                ['nama' => 'Lok Hamawang', 'tipe' => 'desa'],
                ['nama' => 'Lok Panginangan', 'tipe' => 'desa'],
                ['nama' => 'Matang Hanau', 'tipe' => 'desa'],
                ['nama' => 'Matang Lurus', 'tipe' => 'desa'],
                ['nama' => 'Mundar', 'tipe' => 'desa'],
                ['nama' => 'Panaitan', 'tipe' => 'desa'],
                ['nama' => 'Pimping', 'tipe' => 'desa'],
                ['nama' => 'Pupuyuan', 'tipe' => 'desa'],
                ['nama' => 'Simpang Tiga', 'tipe' => 'desa'],
                ['nama' => 'Sungai Awang', 'tipe' => 'desa'],
                ['nama' => 'Sungai Tabuk', 'tipe' => 'desa'],
                ['nama' => 'Tampang', 'tipe' => 'desa'],
                ['nama' => 'Tanah Habang Kanan', 'tipe' => 'desa'],
                ['nama' => 'Tanah Habang Kiri', 'tipe' => 'desa'],
                ['nama' => 'Teluk Karya', 'tipe' => 'desa'],
            ],
            'Paringin' => [
                // 14 desa + 2 kelurahan
                ['nama' => 'Babayau', 'tipe' => 'desa'],
                ['nama' => 'Balang', 'tipe' => 'desa'],
                ['nama' => 'Balida', 'tipe' => 'desa'],
                ['nama' => 'Dahai', 'tipe' => 'desa'],
                ['nama' => 'Hujan Mas', 'tipe' => 'desa'],
                ['nama' => 'Kalahiang', 'tipe' => 'desa'],
                ['nama' => 'Lamida Bawah', 'tipe' => 'desa'],
                ['nama' => 'Lasung Batu', 'tipe' => 'desa'],
                ['nama' => 'Layap', 'tipe' => 'desa'],
                ['nama' => 'Lok Batung', 'tipe' => 'desa'],
                ['nama' => 'Mangkayahu', 'tipe' => 'desa'],
                ['nama' => 'Murung Ilung', 'tipe' => 'desa'],
                ['nama' => 'Paran', 'tipe' => 'desa'],
                ['nama' => 'Sungai Katapi', 'tipe' => 'desa'],
                ['nama' => 'Paringin Kota', 'tipe' => 'kelurahan'],
                ['nama' => 'Paringin Timur', 'tipe' => 'kelurahan'],
            ],
            'Paringin Selatan' => [
                // 15 desa + 1 kelurahan
                ['nama' => 'Baruh Bahinu Dalam', 'tipe' => 'desa'],
                ['nama' => 'Baruh Bahinu Luar', 'tipe' => 'desa'],
                ['nama' => 'Binjai', 'tipe' => 'desa'],
                ['nama' => 'Bungin', 'tipe' => 'desa'],
                ['nama' => 'Galumbang', 'tipe' => 'desa'],
                ['nama' => 'Halubau', 'tipe' => 'desa'],
                ['nama' => 'Halubau Utara', 'tipe' => 'desa'],
                ['nama' => 'Inan', 'tipe' => 'desa'],
                ['nama' => 'Lingsir', 'tipe' => 'desa'],
                ['nama' => 'Maradap', 'tipe' => 'desa'],
                ['nama' => 'Murung Abuin', 'tipe' => 'desa'],
                ['nama' => 'Murung Jambu', 'tipe' => 'desa'],
                ['nama' => 'Panggung', 'tipe' => 'desa'],
                ['nama' => 'Tarangan', 'tipe' => 'desa'],
                ['nama' => 'Telaga Purun', 'tipe' => 'desa'],
                ['nama' => 'Batu Piring', 'tipe' => 'kelurahan'],
            ],
            'Tebing Tinggi' => [
                // 12 desa
                ['nama' => 'Auh', 'tipe' => 'desa'],
                ['nama' => 'Ajung', 'tipe' => 'desa'],
                ['nama' => 'Dayak Pitap', 'tipe' => 'desa'],
                ['nama' => 'Gunung Batu', 'tipe' => 'desa'],
                ['nama' => 'Juuh', 'tipe' => 'desa'],
                ['nama' => 'Kambiyain', 'tipe' => 'desa'],
                ['nama' => 'Langkap', 'tipe' => 'desa'],
                ['nama' => 'Mayanau', 'tipe' => 'desa'],
                ['nama' => 'Simpang Bumbuan', 'tipe' => 'desa'],
                ['nama' => 'Simpang Nadong', 'tipe' => 'desa'],
                ['nama' => 'Sungsum', 'tipe' => 'desa'],
                ['nama' => 'Tebing Tinggi', 'tipe' => 'desa'],
            ],
        ];

        foreach ($data as $namaKecamatan => $desas) {
            $kecamatan = Kecamatan::updateOrCreate(
                ['nama' => $namaKecamatan],
                []
            );

            foreach ($desas as $desa) {
                Desa::updateOrCreate(
                    [
                        'kecamatan_id' => $kecamatan->id,
                        'nama' => $desa['nama'],
                    ],
                    [
                        'tipe' => $desa['tipe'],
                    ]
                );
            }
        }
    }
}
