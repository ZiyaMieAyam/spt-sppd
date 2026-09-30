<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Http\Controllers\SppdPdfController;
use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\Pegawai;
use App\Models\Sppd;
use App\Models\Spt;
use App\Services\PenandatanganService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SppdPdfKopTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_kop_sppd_tetap_sekda(): void
    {
        $html = view('pdf.partials.kop', [
            'penandatangan' => ['kop' => 'sekda'],
            'pathKop' => PenandatanganService::pathKop('sekda'),
            'adaGambarKop' => false,
        ])->render();

        $this->assertStringContainsString('SEKRETARIAT DAERAH', $html);
    }

    public function test_pathkop_tidak_lagi_hardcoded_kosong(): void
    {
        $method = new \ReflectionMethod(SppdPdfController::class, 'variabelKop');
        $method->setAccessible(true);

        $hasil = $method->invoke(new SppdPdfController, ['kop' => 'sekda']);

        $this->assertSame(public_path('images/kop-sekda.png'), $hasil['pathKop']);
        $this->assertNotSame('', $hasil['pathKop']);
    }

    public function test_adagambarkop_false_selama_file_belum_tersedia(): void
    {
        $pathKop = PenandatanganService::pathKop('sekda');

        $this->assertFileDoesNotExist($pathKop);

        $method = new \ReflectionMethod(SppdPdfController::class, 'variabelKop');
        $method->setAccessible(true);
        $hasil = $method->invoke(new SppdPdfController, ['kop' => 'sekda']);

        $this->assertFalse($hasil['adaGambarKop']);
    }

    public function test_fallback_teks_digunakan_selama_gambar_belum_tersedia(): void
    {
        $html = view('pdf.partials.kop', [
            'penandatangan' => ['kop' => 'sekda'],
            'pathKop' => PenandatanganService::pathKop('sekda'),
            'adaGambarKop' => false,
        ])->render();

        $this->assertStringContainsString('SEKRETARIAT DAERAH', $html);
        $this->assertStringNotContainsString('kop-gambar', $html);
    }

    public function test_gambar_dipakai_jika_file_benar_benar_tersedia(): void
    {
        $html = view('pdf.partials.kop', [
            'penandatangan' => ['kop' => 'sekda'],
            'pathKop' => public_path('images/garuda.png'),
            'adaGambarKop' => true,
        ])->render();

        $this->assertStringContainsString('kop-gambar', $html);
    }

    public function test_blade_sppd_tidak_lagi_memaksa_kop_diskominfo(): void
    {
        $sumber = file_get_contents(resource_path('views/pdf/sppd.blade.php'));

        $this->assertStringNotContainsString("'kop' => 'diskominfo'", $sumber);
        $this->assertStringContainsString("@include('pdf.partials.kop')", $sumber);
    }

    public function test_sppd_pdf_tetap_terender_tanpa_aset_kop_resmi(): void
    {
        $admin = \App\Models\User::factory()->create(['role' => UserRole::Admin]);
        $spt = $this->buatDataDasar();
        $sppd = $spt->sppds()->firstOrFail();

        $response = $this->actingAs($admin)->get(route('sppds.pdf', $sppd));

        $response->assertOk();
        $this->assertStringContainsString(
            'application/pdf',
            (string) $response->headers->get('Content-Type')
        );
    }

    protected function buatDataDasar(): Spt
    {
        $kecamatanId = Kecamatan::where('nama', 'Awayan')->value('id');
        $desa = Desa::where('kecamatan_id', $kecamatanId)->orderBy('nama')->value('nama');

        $spt = Spt::create([
            'jenis_perjalanan' => 'Dalam Daerah',
            'nomor_spt' => Spt::generateNomorSpt('2026-08-12'),
            'tanggal_spt' => '2026-08-12',
            'tanggal_berangkat' => '2026-08-13',
            'tanggal_kembali' => '2026-08-15',
            'perihal' => 'Rapat koordinasi',
            'kecamatan_id' => $kecamatanId,
            'desa' => $desa,
            'kota_tujuan_id' => null,
        ]);

        foreach (Pegawai::orderBy('nama')->limit(2)->get() as $i => $pegawai) {
            Sppd::create([
                'spt_id' => $spt->id,
                'pegawai_id' => $pegawai->id,
                'nomor_sppd' => $pegawai->kode_sppd.'/'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT).'/DISKOMINFOSAN-BLG/VIII/2026',
                'tanggal_berangkat' => '2026-08-13',
                'tanggal_kembali' => '2026-08-15',
            ]);
        }

        return $spt;
    }
}
