<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PegawaiSeeder extends Seeder
{
    /**
     * Master Pegawai Diskominfosan - 33 data asli (28 PNS + 5 PPPK)
     * Sumber: Data PNS & PPPK Diskominfosan (tidak ada data fiktif).
     * unit_kerja seragam: DINAS KOMUNIKASI, INFORMATIKA, STATISTIK DAN PERSANDIAN
     * status: ASN untuk PNS (28), PPPK untuk PPPK (5) - enum telah diperluas
     * pangkat PPPK = NULL (tidak tersedia di sumber, tidak mengarang)
     * kode_sppd: 097.2 Kepala Dinas, 097.3 Sekretaris/Kabid, 097.4 Kasubbag/Kasi, 097.5 Staf/JF
     */
    public function run(): void
    {
        $unitKerja = 'DINAS KOMUNIKASI, INFORMATIKA, STATISTIK DAN PERSANDIAN';

        $pegawais = [
            // 1 - Kepala Dinas 097.2
            [
                'nip' => '196704031994031015',
                'nama' => 'H. SYAIFUDDIN, S.Pd, MM',
                'pangkat' => 'PEMBINA UTAMA MUDA',
                'golongan' => 'IV/C',
                'jabatan' => 'KEPALA DINAS KOMUNIKASI, INFORMATIKA, STATISTIK DAN PERSANDIAN',
                'kode_sppd' => '097.2',
                'unit_kerja' => $unitKerja,
                'status' => 'ASN',
            ],
            // 2 - Kepala Bidang 097.3
            [
                'nip' => '196906041994031009',
                'nama' => 'M. SAIFUL BAHRI, S.Pd',
                'pangkat' => 'PEMBINA',
                'golongan' => 'IV/A',
                'jabatan' => 'KEPALA BIDANG PENGELOLAAN INFORMASI DAN KOMUNIKASI PUBLIK',
                'kode_sppd' => '097.3',
                'unit_kerja' => $unitKerja,
                'status' => 'ASN',
            ],
            // 3 - Penelaah 097.5
            [
                'nip' => '197402022000032006',
                'nama' => 'FAIZAH FEBRIANI, S.Psi, MSE',
                'pangkat' => 'PEMBINA',
                'golongan' => 'IV/A',
                'jabatan' => 'PENELAAH TEKNIS KEBIJAKAN',
                'kode_sppd' => '097.5',
                'unit_kerja' => $unitKerja,
                'status' => 'ASN',
            ],
            // 4 - Kepala Bidang 097.3
            [
                'nip' => '198310162009042004',
                'nama' => 'NITTA FRIYANTI, SE',
                'pangkat' => 'PEMBINA',
                'golongan' => 'IV/A',
                'jabatan' => 'KEPALA BIDANG STATISTIK DAN PERSANDIAN',
                'kode_sppd' => '097.3',
                'unit_kerja' => $unitKerja,
                'status' => 'ASN',
            ],
            // 5 - Sekretaris 097.3
            [
                'nip' => '198510202010012031',
                'nama' => 'ERLIYANTI, SE, M.Ak, MM',
                'pangkat' => 'PEMBINA',
                'golongan' => 'IV/A',
                'jabatan' => 'SEKRETARIS',
                'kode_sppd' => '097.3',
                'unit_kerja' => $unitKerja,
                'status' => 'ASN',
            ],
            // 6 - Penelaah 097.5
            [
                'nip' => '198507232006042003',
                'nama' => 'ERLITA HANDAYANI, S.ST, MM',
                'pangkat' => 'PEMBINA',
                'golongan' => 'IV/A',
                'jabatan' => 'PENELAAH TEKNIS KEBIJAKAN',
                'kode_sppd' => '097.5',
                'unit_kerja' => $unitKerja,
                'status' => 'ASN',
            ],
            // 7 - JF 097.5
            [
                'nip' => '197512172010011010',
                'nama' => 'FAUZAN RAHMAN, S.Sos',
                'pangkat' => 'PENATA TK I',
                'golongan' => 'III/D',
                'jabatan' => 'JF PRANATA HUBUNGAN MASYARAKAT AHLI MUDA',
                'kode_sppd' => '097.5',
                'unit_kerja' => $unitKerja,
                'status' => 'ASN',
            ],
            // 8 - JF Sandiman 097.5
            [
                'nip' => '198112062010011017',
                'nama' => 'EDDY FAHRIANNOR, S.Sos',
                'pangkat' => 'PENATA TK I',
                'golongan' => 'III/D',
                'jabatan' => 'JF SANDIMAN AHLI MUDA',
                'kode_sppd' => '097.5',
                'unit_kerja' => $unitKerja,
                'status' => 'ASN',
            ],
            // 9 - Kepala Bidang 097.3
            [
                'nip' => '199005212012061002',
                'nama' => 'MURDIANSYAH, S.STP, M.IP',
                'pangkat' => 'PENATA TK I',
                'golongan' => 'III/D',
                'jabatan' => 'KEPALA BIDANG PENGELOLAAN APLIKASI INFORMATIKA',
                'kode_sppd' => '097.3',
                'unit_kerja' => $unitKerja,
                'status' => 'ASN',
            ],
            // 10 - JF Statistisi 097.5
            [
                'nip' => '197812082006041022',
                'nama' => 'HERRY, S.A.P',
                'pangkat' => 'PENATA TK I',
                'golongan' => 'III/D',
                'jabatan' => 'JF STATISTISI AHLI MUDA',
                'kode_sppd' => '097.5',
                'unit_kerja' => $unitKerja,
                'status' => 'ASN',
            ],
            // 11 - JF Pranata Komputer 097.5
            [
                'nip' => '198103272010011020',
                'nama' => 'DEBBIE ADE CHANDRA, S.Kom',
                'pangkat' => 'PENATA TK I',
                'golongan' => 'III/D',
                'jabatan' => 'JF PRANATA KOMPUTER AHLI MUDA',
                'kode_sppd' => '097.5',
                'unit_kerja' => $unitKerja,
                'status' => 'ASN',
            ],
            // 12 - JF Humas 097.5
            [
                'nip' => '198205252009041001',
                'nama' => 'YUSMA, S.Kom, MM',
                'pangkat' => 'PENATA TK I',
                'golongan' => 'III/D',
                'jabatan' => 'JF PRANATA HUBUNGAN MASYARAKAT AHLI MUDA',
                'kode_sppd' => '097.5',
                'unit_kerja' => $unitKerja,
                'status' => 'ASN',
            ],
            // 13 - Kasubbag 097.4
            [
                'nip' => '198206022005012017',
                'nama' => 'SRI YUNIDA MISLIANI, S.Sos',
                'pangkat' => 'PENATA TK I',
                'golongan' => 'III/D',
                'jabatan' => 'KEPALA SUB BAGIAN PERENCANAAN DAN KEUANGAN',
                'kode_sppd' => '097.4',
                'unit_kerja' => $unitKerja,
                'status' => 'ASN',
            ],
            // 14 - JF Humas 097.5
            [
                'nip' => '197911222006041008',
                'nama' => 'EDDY IRAWAN, S.Sos, MM',
                'pangkat' => 'PENATA TK I',
                'golongan' => 'III/D',
                'jabatan' => 'JF PRANATA HUBUNGAN MASYARAKAT AHLI MUDA',
                'kode_sppd' => '097.5',
                'unit_kerja' => $unitKerja,
                'status' => 'ASN',
            ],
            // 15 - JF Statistisi 097.5
            [
                'nip' => '197601102010012009',
                'nama' => 'FAULINA, S.Kom, MM',
                'pangkat' => 'PENATA TK I',
                'golongan' => 'III/D',
                'jabatan' => 'JF STATISTISI AHLI MUDA',
                'kode_sppd' => '097.5',
                'unit_kerja' => $unitKerja,
                'status' => 'ASN',
            ],
            // 16 - Penelaah 097.5
            [
                'nip' => '198611082010011007',
                'nama' => 'DUDY RACHMAT, S.Pd, M.Pd',
                'pangkat' => 'PENATA TK I',
                'golongan' => 'III/D',
                'jabatan' => 'PENELAAH TEKNIS KEBIJAKAN',
                'kode_sppd' => '097.5',
                'unit_kerja' => $unitKerja,
                'status' => 'ASN',
            ],
            // 17 - Penelaah 097.5
            [
                'nip' => '199110032020122008',
                'nama' => 'NORDIATI, S.Kom',
                'pangkat' => 'PENATA MUDA TK I',
                'golongan' => 'III/B',
                'jabatan' => 'PENELAAH TEKNIS KEBIJAKAN',
                'kode_sppd' => '097.5',
                'unit_kerja' => $unitKerja,
                'status' => 'ASN',
            ],
            // 18 - Kasubbag 097.4
            [
                'nip' => '198601232010012027',
                'nama' => 'ARIATI LISTIANA NINGSIH, S.AP',
                'pangkat' => 'PENATA MUDA TK I',
                'golongan' => 'III/B',
                'jabatan' => 'KEPALA SUB BAGIAN UMUM DAN KEPEGAWAIAN',
                'kode_sppd' => '097.4',
                'unit_kerja' => $unitKerja,
                'status' => 'ASN',
            ],
            // 19 - Penelaah 097.5
            [
                'nip' => '197901272009031002',
                'nama' => 'ABDULLAH, S.AP',
                'pangkat' => 'PENATA MUDA TK I',
                'golongan' => 'III/B',
                'jabatan' => 'PENELAAH TEKNIS KEBIJAKAN',
                'kode_sppd' => '097.5',
                'unit_kerja' => $unitKerja,
                'status' => 'ASN',
            ],
            // 20 - JF Komputer Pertama 097.5
            [
                'nip' => '198809262022021001',
                'nama' => 'ALBERTUS EDY SUPRIYONO, S.Kom',
                'pangkat' => 'PENATA MUDA TK I',
                'golongan' => 'III/B',
                'jabatan' => 'JF PRANATA KOMPUTER AHLI PERTAMA',
                'kode_sppd' => '097.5',
                'unit_kerja' => $unitKerja,
                'status' => 'ASN',
            ],
            // 21
            [
                'nip' => '199905082022022002',
                'nama' => 'PETRA WARA NANDAYANI. R, S.T',
                'pangkat' => 'PENATA MUDA TK I',
                'golongan' => 'III/B',
                'jabatan' => 'JF PRANATA KOMPUTER AHLI PERTAMA',
                'kode_sppd' => '097.5',
                'unit_kerja' => $unitKerja,
                'status' => 'ASN',
            ],
            // 22
            [
                'nip' => '199601122022021004',
                'nama' => 'SYAFIQ, S.Stat.',
                'pangkat' => 'PENATA MUDA',
                'golongan' => 'III/A',
                'jabatan' => 'JF STATISTISI AHLI PERTAMA',
                'kode_sppd' => '097.5',
                'unit_kerja' => $unitKerja,
                'status' => 'ASN',
            ],
            // 23
            [
                'nip' => '199807272025042010',
                'nama' => 'ANITA KESUMA DEWI, S.Kom',
                'pangkat' => 'PENATA MUDA',
                'golongan' => 'III/A',
                'jabatan' => 'JF PRANATA KOMPUTER AHLI PERTAMA',
                'kode_sppd' => '097.5',
                'unit_kerja' => $unitKerja,
                'status' => 'ASN',
            ],
            // 24
            [
                'nip' => '200008142025041006',
                'nama' => 'RAHMAT, S.Mat',
                'pangkat' => 'PENATA MUDA',
                'golongan' => 'III/A',
                'jabatan' => 'JF STATISTISI AHLI PERTAMA',
                'kode_sppd' => '097.5',
                'unit_kerja' => $unitKerja,
                'status' => 'ASN',
            ],
            // 25
            [
                'nip' => '200102112025041002',
                'nama' => 'ANDRE SAPUTRA, S.AP',
                'pangkat' => 'PENATA MUDA',
                'golongan' => 'III/A',
                'jabatan' => 'JF PRANATA HUBUNGAN MASYARAKAT AHLI PERTAMA',
                'kode_sppd' => '097.5',
                'unit_kerja' => $unitKerja,
                'status' => 'ASN',
            ],
            // 26
            [
                'nip' => '200004222025041002',
                'nama' => 'MUHAMMAD SAIDI YUPINI, S.Kom',
                'pangkat' => 'PENATA MUDA',
                'golongan' => 'III/A',
                'jabatan' => 'JF SANDIMAN AHLI PERTAMA',
                'kode_sppd' => '097.5',
                'unit_kerja' => $unitKerja,
                'status' => 'ASN',
            ],
            // 27
            [
                'nip' => '199706252019031003',
                'nama' => 'MUHAMMAD HATTA, A.Md',
                'pangkat' => 'PENGATUR TK I',
                'golongan' => 'II/D',
                'jabatan' => 'PENGOLAH DATA DAN INFORMASI',
                'kode_sppd' => '097.5',
                'unit_kerja' => $unitKerja,
                'status' => 'ASN',
            ],
            // 28
            [
                'nip' => '200207072025041002',
                'nama' => 'ANGGIE YULISTIAWAN, A.Md.Kom',
                'pangkat' => 'PENGATUR',
                'golongan' => 'II/C',
                'jabatan' => 'JF PRANATA KOMPUTER TERAMPIL',
                'kode_sppd' => '097.5',
                'unit_kerja' => $unitKerja,
                'status' => 'ASN',
            ],
            // 29 PPPK
            [
                'nip' => '199704152024211001',
                'nama' => 'MUHAMMAD RIDHA ZULIANNOR, S.Kom',
                'pangkat' => null,
                'golongan' => 'IX',
                'jabatan' => 'JF PRANATA KOMPUTER AHLI PERTAMA (Kelas : 8)',
                'kode_sppd' => '097.5',
                'unit_kerja' => $unitKerja,
                'status' => 'PPPK',
            ],
            // 30 PPPK
            [
                'nip' => '199807102024211001',
                'nama' => 'BARKATULLAH ASFI, S.Kom.',
                'pangkat' => null,
                'golongan' => 'IX',
                'jabatan' => 'JF PRANATA KOMPUTER AHLI PERTAMA (Kelas : 8)',
                'kode_sppd' => '097.5',
                'unit_kerja' => $unitKerja,
                'status' => 'PPPK',
            ],
            // 31 PPPK
            [
                'nip' => '199108172025211011',
                'nama' => 'KHAIRUL AZMI, S.Kom',
                'pangkat' => null,
                'golongan' => 'IX',
                'jabatan' => 'JF PRANATA KOMPUTER AHLI PERTAMA (Kelas : 8)',
                'kode_sppd' => '097.5',
                'unit_kerja' => $unitKerja,
                'status' => 'PPPK',
            ],
            // 32 PPPK
            [
                'nip' => '199908182025212002',
                'nama' => 'NANDA YULIARTI',
                'pangkat' => null,
                'golongan' => 'V',
                'jabatan' => 'PENGADMINISTRASI PERKANTORAN (Kelas : 5)',
                'kode_sppd' => '097.5',
                'unit_kerja' => $unitKerja,
                'status' => 'PPPK',
            ],
            // 33 PPPK
            [
                'nip' => '200105052025211003',
                'nama' => 'YUDIYANTORO',
                'pangkat' => null,
                'golongan' => 'V',
                'jabatan' => 'OPERATOR LAYANAN OPERASIONAL (Kelas : 5)',
                'kode_sppd' => '097.5',
                'unit_kerja' => $unitKerja,
                'status' => 'PPPK',
            ],
        ];

        // Hapus pegawai dummy yang NIP-nya tidak ada di daftar valid 33
        // Perlu hapus SPPD terkait terlebih dahulu untuk menghindari FK constraint
        $validNips = collect($pegawais)->pluck('nip')->all();

        // Cari ID pegawai dummy yang akan dihapus
        $dummyIds = Pegawai::whereNotIn('nip', $validNips)->pluck('id')->all();
        if (!empty($dummyIds)) {
            // Hapus sppds yang referensi ke pegawai dummy (cascade manual)
            DB::table('sppds')->whereIn('pegawai_id', $dummyIds)->delete();
            // Hapus pegawai dummy
            Pegawai::whereIn('id', $dummyIds)->delete();
        }

        foreach ($pegawais as $pegawai) {
            Pegawai::updateOrCreate(
                ['nip' => $pegawai['nip']],
                $pegawai
            );
        }
    }
}
