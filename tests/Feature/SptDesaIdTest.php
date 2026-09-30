<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\Spt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SptDesaIdTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_spt_desa_valid_mendapat_desa_id(): void
    {
        $kecamatanId = Kecamatan::where('nama', 'Paringin Selatan')->value('id');

        $spt = $this->buatSpt('Batu Piring', $kecamatanId);

        $this->assertSame(
            Desa::where('kecamatan_id', $kecamatanId)->where('nama', 'Batu Piring')->value('id'),
            $spt->desa_id
        );
        $this->assertSame('Batu Piring', $spt->desa);
    }

    public function test_match_nama_sama_kecamatan_berbeda_tidak_salah(): void
    {
        $ps = Kecamatan::where('nama', 'Paringin Selatan')->value('id');
        $juai = Kecamatan::where('nama', 'Juai')->value('id');

        $sptPs = $this->buatSpt('Galumbang', $ps);
        $sptJuai = $this->buatSpt('Galumbang', $juai);

        $this->assertNotSame($sptPs->desa_id, $sptJuai->desa_id);
        $this->assertSame(
            Desa::where('kecamatan_id', $ps)->where('nama', 'Galumbang')->value('id'),
            $sptPs->desa_id
        );
        $this->assertSame(
            Desa::where('kecamatan_id', $juai)->where('nama', 'Galumbang')->value('id'),
            $sptJuai->desa_id
        );
    }

    public function test_desa_tidak_ditemukan_desa_id_null_string_utuh(): void
    {
        $awayan = Kecamatan::where('nama', 'Awayan')->value('id');

        $spt = $this->buatSpt('Batu Piring', $awayan);

        $this->assertNull($spt->desa_id);
        $this->assertSame('Batu Piring', $spt->desa);

        $sptFiktif = $this->buatSpt('Desa Fiktif', $awayan);

        $this->assertNull($sptFiktif->desa_id);
        $this->assertSame('Desa Fiktif', $sptFiktif->desa);
    }

    public function test_edit_field_lain_tidak_mengubah_desa(): void
    {
        $kecamatanId = Kecamatan::where('nama', 'Paringin Selatan')->value('id');
        $spt = $this->buatSpt('Batu Piring', $kecamatanId);
        $desaId = $spt->desa_id;

        $spt->update(['perihal' => 'Perihal diubah']);

        $this->assertSame('Batu Piring', $spt->desa);
        $this->assertSame($desaId, $spt->desa_id);
    }

    public function test_relasi_desa_bekerja(): void
    {
        $kecamatanId = Kecamatan::where('nama', 'Paringin Selatan')->value('id');
        $spt = $this->buatSpt('Batu Piring', $kecamatanId);

        // Properti $spt->desa tetap string historis di tahap 1.
        $this->assertSame('Batu Piring', $spt->desa);
        $this->assertSame('Batu Piring', $spt->desa()->value('nama'));
        $this->assertSame('Batu Piring', $spt->load('desa')->getRelation('desa')->nama);
    }

    public function test_backfill_command_hanya_match_tepat(): void
    {
        $ps = Kecamatan::where('nama', 'Paringin Selatan')->value('id');
        $awayan = Kecamatan::where('nama', 'Awayan')->value('id');

        // Lewat query builder agar hook model tidak ikut campur.
        $idCocok = DB::table('spts')->insertGetId($this->barisLegacy('Batu Piring', $ps));
        $idTakCocok = DB::table('spts')->insertGetId($this->barisLegacy('Desa Fiktif', $awayan));

        $this->assertSame(0, Artisan::call('spt:backfill-desa-id'));

        $cocok = DB::table('spts')->where('id', $idCocok)->first();
        $takCocok = DB::table('spts')->where('id', $idTakCocok)->first();

        $this->assertSame(
            Desa::where('kecamatan_id', $ps)->where('nama', 'Batu Piring')->value('id'),
            (int) $cocok->desa_id
        );
        $this->assertSame('Batu Piring', $cocok->desa);

        $this->assertNull($takCocok->desa_id);
        $this->assertSame('Desa Fiktif', $takCocok->desa);
    }

    protected function buatSpt(string $desa, int $kecamatanId): Spt
    {
        return Spt::create([
            'jenis_perjalanan' => 'Dalam Daerah',
            'nomor_spt' => Spt::generateNomorSpt('2026-08-12'),
            'tanggal_spt' => '2026-08-12',
            'tanggal_berangkat' => '2026-08-13',
            'tanggal_kembali' => '2026-08-15',
            'perihal' => 'Uji desa_id',
            'kecamatan_id' => $kecamatanId,
            'desa' => $desa,
            'kota_tujuan_id' => null,
        ]);
    }

    protected function barisLegacy(string $desa, int $kecamatanId): array
    {
        return [
            'jenis_perjalanan' => 'Dalam Daerah',
            'nomor_spt' => Spt::generateNomorSpt('2026-08-12'),
            'tanggal_spt' => '2026-08-12',
            'tanggal_berangkat' => '2026-08-13',
            'tanggal_kembali' => '2026-08-15',
            'perihal' => 'Baris legacy',
            'kecamatan_id' => $kecamatanId,
            'desa' => $desa,
            'desa_id' => null,
            'kota_tujuan_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
