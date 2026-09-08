<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use Illuminate\Database\Seeder;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Data PEGAWAI FIKTIF untuk testing SPT/SPPD.
     * Semua NIP dan nama adalah fiktif, bukan data pribadi nyata.
     * Kode SPPD disesuaikan jabatan: 097.2 Eselon I, 097.3 Sekretaris/Kabid, 097.4 Kasi/Kasubbag, 097.5 Staf
     * Status hanya ASN / Non ASN sesuai enum database.
     */
    public function run(): void
    {
        $pegawais = [
            // 1. Kepala Dinas — 097.2 (reuse NIP lama agar tidak duplikat)
            [
                'nip' => '197001011990031001',
                'nama' => 'Hendra Wijaya Kusuma, S.STP., M.Si.',
                'pangkat' => 'Pembina Utama Muda',
                'golongan' => 'IV/c',
                'jabatan' => 'Kepala Dinas',
                'kode_sppd' => '097.2',
                'unit_kerja' => 'Dinas Komunikasi, Informatika, Statistik dan Persandian',
                'status' => 'ASN',
            ],
            // 2. Sekretaris Dinas — 097.3
            [
                'nip' => '197503152000122001',
                'nama' => 'Lestari Rahayu, S.Kom., M.M.',
                'pangkat' => 'Pembina Tingkat I',
                'golongan' => 'IV/b',
                'jabatan' => 'Sekretaris Dinas',
                'kode_sppd' => '097.3',
                'unit_kerja' => 'Dinas Komunikasi, Informatika, Statistik dan Persandian',
                'status' => 'ASN',
            ],
            // 3. Kepala Bidang — 097.3
            [
                'nip' => '198002102005011001',
                'nama' => 'Bambang Prasetyo, S.Sos., M.A.P.',
                'pangkat' => 'Pembina',
                'golongan' => 'IV/a',
                'jabatan' => 'Kepala Bidang Informasi dan Komunikasi Publik',
                'kode_sppd' => '097.3',
                'unit_kerja' => 'Dinas Komunikasi, Informatika, Statistik dan Persandian',
                'status' => 'ASN',
            ],
            // 4. Kepala Bidang — 097.3
            [
                'nip' => '198603202010011001',
                'nama' => 'Rina Kartika Dewi, S.T., M.T.',
                'pangkat' => 'Pembina',
                'golongan' => 'IV/a',
                'jabatan' => 'Kepala Bidang Statistik dan Persandian',
                'kode_sppd' => '097.3',
                'unit_kerja' => 'Dinas Komunikasi, Informatika, Statistik dan Persandian',
                'status' => 'ASN',
            ],
            // 5. Kepala Bidang — 097.3
            [
                'nip' => '198704052011011001',
                'nama' => 'Arif Nugroho Santoso, S.Kom., M.T.',
                'pangkat' => 'Penata Tingkat I',
                'golongan' => 'III/d',
                'jabatan' => 'Kepala Bidang Aplikasi Informatika',
                'kode_sppd' => '097.3',
                'unit_kerja' => 'Dinas Komunikasi, Informatika, Statistik dan Persandian',
                'status' => 'ASN',
            ],
            // 6. Kasubbag Umum & Kepegawaian — 097.4
            [
                'nip' => '199105102015021001',
                'nama' => 'Siti Maulida, S.E., M.M.',
                'pangkat' => 'Penata Tingkat I',
                'golongan' => 'III/d',
                'jabatan' => 'Kepala Subbagian Umum dan Kepegawaian',
                'kode_sppd' => '097.4',
                'unit_kerja' => 'Dinas Komunikasi, Informatika, Statistik dan Persandian',
                'status' => 'ASN',
            ],
            // 7. Kasubbag Keuangan — 097.4 (reuse NIP Non ASN lama, update ke ASN)
            [
                'nip' => '199308152018021001',
                'nama' => 'Wahyu Firmansyah, S.H.',
                'pangkat' => 'Penata',
                'golongan' => 'III/c',
                'jabatan' => 'Kepala Subbagian Keuangan dan Aset',
                'kode_sppd' => '097.4',
                'unit_kerja' => 'Dinas Komunikasi, Informatika, Statistik dan Persandian',
                'status' => 'ASN',
            ],
            // 8. Kasi Pengelolaan Informasi — 097.4
            [
                'nip' => '199509202019011001',
                'nama' => 'Dimas Pratama, S.Kom.',
                'pangkat' => 'Penata',
                'golongan' => 'III/c',
                'jabatan' => 'Kepala Seksi Pengelolaan Informasi Publik',
                'kode_sppd' => '097.4',
                'unit_kerja' => 'Dinas Komunikasi, Informatika, Statistik dan Persandian',
                'status' => 'ASN',
            ],
            // 9. Kasi Layanan e-Government — 097.4
            [
                'nip' => '199203152016021009',
                'nama' => 'Nurlita Sari, S.I.Kom., M.I.Kom.',
                'pangkat' => 'Penata Muda Tingkat I',
                'golongan' => 'III/b',
                'jabatan' => 'Kepala Seksi Layanan e-Government',
                'kode_sppd' => '097.4',
                'unit_kerja' => 'Dinas Komunikasi, Informatika, Statistik dan Persandian',
                'status' => 'ASN',
            ],
            // 10. Staf Pelaksana ASN — 097.5
            [
                'nip' => '199405202018021010',
                'nama' => 'Fajar Abdullah, A.Md.Kom.',
                'pangkat' => 'Penata Muda Tingkat I',
                'golongan' => 'III/b',
                'jabatan' => 'Staf Pelaksana - Pranata Komputer',
                'kode_sppd' => '097.5',
                'unit_kerja' => 'Dinas Komunikasi, Informatika, Statistik dan Persandian',
                'status' => 'ASN',
            ],
            // 11. Staf Pelaksana ASN — 097.5
            [
                'nip' => '199610142019022011',
                'nama' => 'Maya Puspita, S.E.',
                'pangkat' => 'Penata Muda',
                'golongan' => 'III/a',
                'jabatan' => 'Staf Pelaksana - Analis Data',
                'kode_sppd' => '097.5',
                'unit_kerja' => 'Dinas Komunikasi, Informatika, Statistik dan Persandian',
                'status' => 'ASN',
            ],
            // 12. Staf Pelaksana Non ASN — 097.5
            [
                'nip' => '199807152020022012',
                'nama' => 'Rizki Ramadhan Putra, S.Kom.',
                'pangkat' => '-',
                'golongan' => '-',
                'jabatan' => 'Staf Pelaksana - Pengelola Jaringan',
                'kode_sppd' => '097.5',
                'unit_kerja' => 'Dinas Komunikasi, Informatika, Statistik dan Persandian',
                'status' => 'Non ASN',
            ],
            // 13. Staf Pelaksana Non ASN — 097.5
            [
                'nip' => '200012052022022013',
                'nama' => 'Anisa Fitria, A.Md.',
                'pangkat' => '-',
                'golongan' => '-',
                'jabatan' => 'Staf Pelaksana - Tenaga Administrasi',
                'kode_sppd' => '097.5',
                'unit_kerja' => 'Dinas Komunikasi, Informatika, Statistik dan Persandian',
                'status' => 'Non ASN',
            ],
        ];

        foreach ($pegawais as $pegawai) {
            Pegawai::updateOrCreate(
                ['nip' => $pegawai['nip']],
                $pegawai
            );
        }
    }
}
