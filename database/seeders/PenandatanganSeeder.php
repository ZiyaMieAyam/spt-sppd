<?php

namespace Database\Seeders;

use App\Models\Penandatangan;
use Illuminate\Database\Seeder;

class PenandatanganSeeder extends Seeder
{
    public function run(): void
    {
        // Nilai awal disalin dari config/penandatangan.php dan
        // config/pejabat-sementara.php agar dokumen yang sudah berjalan
        // tidak berubah. Selanjutnya dikelola via panel Filament.
        $rows = [
            [
                'kunci' => 'bupati',
                'jabatan' => 'BUPATI BALANGAN',
                'nama' => 'H. Abdul Hadi, S.Ag., M.I.Kom.',
                'nip' => null,
                'pangkat' => null,
                'golongan' => null,
                'kop' => 'bupati',
            ],
            [
                'kunci' => 'wakil-bupati',
                'jabatan' => 'WAKIL BUPATI BALANGAN',
                'nama' => 'H. Akhmad Fauzi, S.Pd.',
                'nip' => null,
                'pangkat' => null,
                'golongan' => null,
                'kop' => 'bupati',
            ],
            [
                'kunci' => 'sekda',
                'jabatan' => 'SEKRETARIS DAERAH KABUPATEN BALANGAN',
                'nama' => 'FAKHRIYANTO, S.Pt, MP',
                'nip' => '197806012005011016',
                'pangkat' => 'Pembina Tk. I',
                'golongan' => 'IV/b',
                'kop' => 'sekda',
            ],
            [
                'kunci' => 'kepala-diskominfo',
                'jabatan' => 'KEPALA DINAS KOMUNIKASI, INFORMATIKA, STATISTIK DAN PERSANDIAN KABUPATEN BALANGAN',
                'nama' => 'H. Syaifuddin Tailah, S.Pd, MM',
                'nip' => '196704031994031015',
                'pangkat' => 'Pembina Utama Muda',
                'golongan' => 'IV/c',
                'kop' => 'diskominfo',
            ],
            [
                'kunci' => 'kepala_dinas',
                'jabatan' => 'KEPALA DINAS KOMUNIKASI, INFORMATIKA, STATISTIK DAN PERSANDIAN KABUPATEN BALANGAN',
                'nama' => 'H. Syaifuddin Tailah, S.Pd, MM',
                'nip' => '196704031994031015',
                'pangkat' => 'Pembina Utama Muda',
                'golongan' => 'IV/c',
                'kop' => null,
            ],
            [
                'kunci' => 'pejabat_teknis',
                'jabatan' => 'KEPALA SUB BAGIAN UMUM DAN KEPEGAWAIAN SELAKU PEJABAT PELAKSANA TEKNIS KEGIATAN',
                'nama' => 'MAHPUDZ AMIN, SE',
                'nip' => '198502142010011016',
                'pangkat' => 'Penata',
                'golongan' => 'III/c',
                'kop' => null,
            ],
        ];

        foreach ($rows as $row) {
            Penandatangan::updateOrCreate(
                ['kunci' => $row['kunci']],
                $row
            );
        }
    }
}
