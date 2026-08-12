<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use Illuminate\Database\Seeder;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pegawais = [
            [
                'nip' => '197001011990031001',
                'nama' => 'H. Ahmad Fauzi',
                'pangkat' => 'Pembina Utama Muda',
                'golongan' => 'IV/c',
                'jabatan' => 'Kepala Dinas',
                'kode_sppd' => '097.2',
                'unit_kerja' => 'Diskominfosan',
                'status' => 'ASN',
            ],
            [
                'nip' => '197503152000122001',
                'nama' => 'Siti Rahmawati',
                'pangkat' => 'Pembina',
                'golongan' => 'IV/a',
                'jabatan' => 'Sekretaris',
                'kode_sppd' => '097.3',
                'unit_kerja' => 'Diskominfosan',
                'status' => 'ASN',
            ],
            [
                'nip' => '198002102005011001',
                'nama' => 'Muhammad Arif',
                'pangkat' => 'Pembina',
                'golongan' => 'IV/a',
                'jabatan' => 'Kepala Bidang',
                'kode_sppd' => '097.3',
                'unit_kerja' => 'Diskominfosan',
                'status' => 'ASN',
            ],
            [
                'nip' => '198603202010011001',
                'nama' => 'Rudi Hartono',
                'pangkat' => 'Penata',
                'golongan' => 'III/c',
                'jabatan' => 'Kepala Seksi',
                'kode_sppd' => '097.4',
                'unit_kerja' => 'Diskominfosan',
                'status' => 'ASN',
            ],
            [
                'nip' => '198704052011011001',
                'nama' => 'Dewi Lestari',
                'pangkat' => 'Penata Tk. I',
                'golongan' => 'III/d',
                'jabatan' => 'Kasubbag',
                'kode_sppd' => '097.4',
                'unit_kerja' => 'Diskominfosan',
                'status' => 'ASN',
            ],
            [
                'nip' => '199105102015021001',
                'nama' => 'Nur Aisyah',
                'pangkat' => 'Penata Muda',
                'golongan' => 'III/a',
                'jabatan' => 'Staff',
                'kode_sppd' => '097.5',
                'unit_kerja' => 'Diskominfosan',
                'status' => 'ASN',
            ],
            [
                'nip' => '199308152018021001',
                'nama' => 'Andi Saputra',
                'pangkat' => '-',
                'golongan' => '-',
                'jabatan' => 'Staff',
                'kode_sppd' => '097.5',
                'unit_kerja' => 'Diskominfosan',
                'status' => 'Non ASN',
            ],
            [
                'nip' => '199509202019011001',
                'nama' => 'Fitri Handayani',
                'pangkat' => '-',
                'golongan' => '-',
                'jabatan' => 'Staff',
                'kode_sppd' => '097.5',
                'unit_kerja' => 'Diskominfosan',
                'status' => 'Non ASN',
            ],
        ];

        foreach ($pegawais as $pegawai) {
            Pegawai::firstOrCreate(
                ['nip' => $pegawai['nip']],
                $pegawai
            );
        }
    }
}