<?php

namespace Tests\Feature;

use App\Models\Kecamatan;
use App\Models\Spt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SptDesaAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_match_tidak_masuk_daftar_mismatch(): void
    {
        $ps = Kecamatan::where('nama', 'Paringin Selatan')->value('id');
        $this->sisipLegacy('Batu Piring', $ps, 'M1');

        $this->assertSame(0, Artisan::call('spt:audit-desa-id'));

        $output = Artisan::output();

        $this->assertStringContainsString('Total SPT dengan desa terisi : 1', $output);
        $this->assertStringContainsString('Total mismatch (tanpa match)   : 0', $output);
        $this->assertStringNotContainsString('Batu Piring', $this->bagianTabel($output));
    }

    public function test_desa_tak_ditemukan_masuk_mismatch(): void
    {
        $awayan = Kecamatan::where('nama', 'Awayan')->value('id');
        $this->sisipLegacy('Desa Fiktif', $awayan, 'M2');

        $this->assertSame(0, Artisan::call('spt:audit-desa-id'));

        $output = Artisan::output();

        $this->assertStringContainsString('Total mismatch (tanpa match)   : 1', $output);
        $this->assertStringContainsString('M2', $output);
        $this->assertStringContainsString('Desa Fiktif', $output);
        $this->assertStringContainsString('Awayan', $output);
    }

    public function test_command_tidak_mengubah_data(): void
    {
        $ps = Kecamatan::where('nama', 'Paringin Selatan')->value('id');
        $awayan = Kecamatan::where('nama', 'Awayan')->value('id');
        $this->sisipLegacy('Batu Piring', $ps, 'M3');
        $this->sisipLegacy('Desa Fiktif', $awayan, 'M4');

        $sebelum = DB::table('spts')->orderBy('id')->get()->map(fn ($r) => (array) $r)->all();

        Artisan::call('spt:audit-desa-id');

        $sesudah = DB::table('spts')->orderBy('id')->get()->map(fn ($r) => (array) $r)->all();

        $this->assertSame($sebelum, $sesudah);
    }

    public function test_nama_sama_kecamatan_beda_match_tepat(): void
    {
        $ps = Kecamatan::where('nama', 'Paringin Selatan')->value('id');
        $juai = Kecamatan::where('nama', 'Juai')->value('id');
        $this->sisipLegacy('Galumbang', $ps, 'M5');
        $this->sisipLegacy('Galumbang', $juai, 'M6');

        $this->assertSame(0, Artisan::call('spt:audit-desa-id'));

        $output = Artisan::output();

        $this->assertStringContainsString('Total SPT dengan desa terisi : 2', $output);
        $this->assertStringContainsString('Total mismatch (tanpa match)   : 0', $output);
    }

    protected function bagianTabel(string $output): string
    {
        $pos = strpos($output, 'ID SPT');

        return $pos === false ? '' : substr($output, $pos);
    }

    protected function sisipLegacy(string $desa, int $kecamatanId, string $nomor): void
    {
        DB::table('spts')->insert([
            'jenis_perjalanan' => 'Dalam Daerah',
            'nomor_spt' => $nomor,
            'tanggal_spt' => '2026-08-12',
            'tanggal_berangkat' => '2026-08-13',
            'tanggal_kembali' => '2026-08-15',
            'perihal' => 'Baris audit',
            'kecamatan_id' => $kecamatanId,
            'desa' => $desa,
            'desa_id' => null,
            'kota_tujuan_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
