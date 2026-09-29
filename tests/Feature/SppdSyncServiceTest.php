<?php

namespace Tests\Feature;

use App\Models\Pegawai;
use App\Models\Spt;
use App\Services\SppdSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SppdSyncServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    protected function buatSptDenganPegawai(
        int $jumlah,
        string $tanggalSpt = '2026-09-29',
        string $berangkat = '2026-10-01',
        string $kembali = '2026-10-03'
    ): Spt {
        $pegawaiIds = Pegawai::orderBy('id')->limit($jumlah)->pluck('id')->all();

        $spt = Spt::create([
            'jenis_perjalanan' => 'Dalam Daerah',
            'nomor_spt' => Spt::generateNomorSpt($tanggalSpt),
            'tanggal_spt' => $tanggalSpt,
            'tanggal_berangkat' => $berangkat,
            'tanggal_kembali' => $kembali,
            'perihal' => 'Uji sinkronisasi SPPD',
        ]);

        SppdSyncService::createForSpt($spt, $pegawaiIds, $tanggalSpt, $berangkat, $kembali);

        return $spt->refresh();
    }

    protected function petakanSppd(Spt $spt): array
    {
        return $spt->sppds()->get()->keyBy('pegawai_id')->map(
            fn ($sppd) => ['id' => $sppd->id, 'nomor' => $sppd->nomor_sppd]
        )->all();
    }

    public function test_edit_tanpa_perubahan_pegawai_mempertahankan_id_dan_nomor(): void
    {
        $spt = $this->buatSptDenganPegawai(2);
        $sebelum = $this->petakanSppd($spt);
        $pegawaiIds = array_keys($sebelum);

        SppdSyncService::syncForSpt($spt, $pegawaiIds, '2026-09-29', '2026-10-01', '2026-10-03');

        $this->assertSame(2, $spt->sppds()->count());
        $this->assertSame($sebelum, $this->petakanSppd($spt));
    }

    public function test_tambah_pegawai_hanya_membuat_satu_sppd_baru(): void
    {
        $spt = $this->buatSptDenganPegawai(2);
        $sebelum = $this->petakanSppd($spt);
        $pegawaiBaru = Pegawai::orderBy('id')->skip(2)->value('id');

        SppdSyncService::syncForSpt(
            $spt,
            [...array_keys($sebelum), $pegawaiBaru],
            '2026-09-29',
            '2026-10-01',
            '2026-10-03'
        );

        $sesudah = $this->petakanSppd($spt);

        $this->assertSame(3, $spt->sppds()->count());

        // SPPD lama utuh (id + nomor sama).
        foreach ($sebelum as $pegawaiId => $info) {
            $this->assertSame($info, $sesudah[$pegawaiId]);
        }

        // SPPD baru tepat satu dengan nomor yang belum dipakai.
        $this->assertArrayHasKey($pegawaiBaru, $sesudah);
        $this->assertNotContains($sesudah[$pegawaiBaru]['nomor'], array_column($sebelum, 'nomor'));
    }

    public function test_hapus_pegawai_hanya_menghapus_sppd_tersebut(): void
    {
        $spt = $this->buatSptDenganPegawai(3);
        $sebelum = $this->petakanSppd($spt);
        $pegawaiIds = array_keys($sebelum);
        $dihapus = $pegawaiIds[0];
        $sisa = [$pegawaiIds[1], $pegawaiIds[2]];

        SppdSyncService::syncForSpt($spt, $sisa, '2026-09-29', '2026-10-01', '2026-10-03');

        $sesudah = $this->petakanSppd($spt);

        $this->assertSame(2, $spt->sppds()->count());
        $this->assertArrayNotHasKey($dihapus, $sesudah);
        $this->assertSame($sebelum[$sisa[0]], $sesudah[$sisa[0]]);
        $this->assertSame($sebelum[$sisa[1]], $sesudah[$sisa[1]]);
    }

    public function test_ubah_tanggal_menyinkronkan_semua_sppd_tanpa_mengubah_nomor(): void
    {
        $spt = $this->buatSptDenganPegawai(2);
        $sebelum = $this->petakanSppd($spt);
        $nomorSpt = $spt->nomor_spt;

        SppdSyncService::syncForSpt(
            $spt,
            array_keys($sebelum),
            '2026-09-29',
            '2026-10-05',
            '2026-10-07'
        );

        $this->assertSame(2, $spt->sppds()->count());
        $this->assertSame($nomorSpt, $spt->refresh()->nomor_spt);

        foreach ($spt->sppds as $sppd) {
            $this->assertSame(
                $sebelum[$sppd->pegawai_id]['id'],
                $sppd->id,
                'ID SPPD harus tetap'
            );
            $this->assertSame(
                $sebelum[$sppd->pegawai_id]['nomor'],
                $sppd->nomor_sppd,
                'Nomor SPPD harus tetap'
            );
            $this->assertSame('2026-10-05', $sppd->tanggal_berangkat->format('Y-m-d'));
            $this->assertSame('2026-10-07', $sppd->tanggal_kembali->format('Y-m-d'));
        }
    }

    public function test_kombinasi_tambah_dan_hapus_pegawai(): void
    {
        $spt = $this->buatSptDenganPegawai(3);
        $sebelum = $this->petakanSppd($spt);
        $pegawaiIds = array_keys($sebelum);
        [$keluar, $tetap1, $tetap2] = $pegawaiIds;
        $masuk = Pegawai::orderBy('id')->skip(3)->value('id');

        SppdSyncService::syncForSpt(
            $spt,
            [$tetap1, $tetap2, $masuk],
            '2026-09-29',
            '2026-10-01',
            '2026-10-03'
        );

        $sesudah = $this->petakanSppd($spt);

        $this->assertSame(3, $spt->sppds()->count());
        $this->assertArrayNotHasKey($keluar, $sesudah);
        $this->assertSame($sebelum[$tetap1], $sesudah[$tetap1]);
        $this->assertSame($sebelum[$tetap2], $sesudah[$tetap2]);
        $this->assertArrayHasKey($masuk, $sesudah);
    }
}
