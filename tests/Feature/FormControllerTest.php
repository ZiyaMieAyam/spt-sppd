<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\KotaTujuan;
use App\Models\Pegawai;
use App\Models\Sppd;
use App\Models\Spt;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FormControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        // User khusus test agar tidak bergantung pada kredensial/urutan seed.
        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
    }

    public function test_halaman_form_tampil(): void
    {
        $this->actingAs($this->admin)
            ->get(route('form'))
            ->assertOk()
            ->assertSee('Form SPT & SPPD')
            ->assertSee('Pegawai Yang Ditugaskan')
            ->assertSee('Cari dan pilih pegawai', false);
    }

    public function test_create_dalam_daerah_menghasilkan_satu_spt_dan_banyak_sppd(): void
    {
        $pegawaiIds = Pegawai::orderBy('nama')->pluck('id')->take(3)->all();
        $kecamatanId = Desa::where('nama', 'Batu Piring')->value('kecamatan_id');

        $this->actingAs($this->admin)
            ->post(route('form.simpan'), [
                'jenis_perjalanan' => 'Dalam Daerah',
                'tanggal_spt' => '2026-08-12',
                'tanggal_berangkat' => '2026-08-13',
                'tanggal_kembali' => '2026-08-15',
                'perihal' => 'Rapat koordinasi kecamatan',
                'kecamatan_id' => $kecamatanId,
                'desa_id' => Desa::where('nama', 'Batu Piring')->value('id'),
                'pegawai_ids' => $pegawaiIds,
            ])
            ->assertRedirect(route('dalam-daerah'));

        $this->assertSame(1, Spt::count());
        $this->assertSame(3, Sppd::count());

        $spt = Spt::first();

        $this->assertMatchesRegularExpression(
            '#^090/\d{3}/SPT/DISKOMINFOSAN-BLG/VIII/2026$#',
            $spt->nomor_spt
        );

        $this->assertSame(3, $spt->sppds()->count());

        foreach ($spt->sppds as $sppd) {
            $this->assertMatchesRegularExpression(
                '#^\d{3}\.\d/\d{3}/DISKOMINFOSAN-BLG/VIII/2026$#',
                $sppd->nomor_sppd
            );
            $this->assertNotNull($sppd->tanggal_berangkat);
            $this->assertNotNull($sppd->tanggal_kembali);
        }
    }

    public function test_create_luar_daerah_tanpa_kota_tujuan_ditolak(): void
    {
        $pegawaiId = Pegawai::orderBy('nama')->value('id');

        $this->actingAs($this->admin)
            ->post(route('form.simpan'), [
                'jenis_perjalanan' => 'Luar Daerah',
                'tanggal_spt' => '2026-08-12',
                'tanggal_berangkat' => '2026-08-13',
                'tanggal_kembali' => '2026-08-15',
                'perihal' => 'Bimtek',
                'pegawai_ids' => [$pegawaiId],
            ])
            ->assertSessionHasErrors('kota_tujuan_id');

        $this->assertSame(0, Spt::count());
        $this->assertSame(0, Sppd::count());
    }

    public function test_create_duplicate_pegawai_ditolak(): void
    {
        $pegawaiId = Pegawai::orderBy('nama')->value('id');
        $kecamatanId = Desa::where('nama', 'Batu Piring')->value('kecamatan_id');

        $this->actingAs($this->admin)
            ->post(route('form.simpan'), [
                'jenis_perjalanan' => 'Dalam Daerah',
                'tanggal_spt' => '2026-08-12',
                'tanggal_berangkat' => '2026-08-13',
                'tanggal_kembali' => '2026-08-15',
                'perihal' => 'Rapat koordinasi kecamatan',
                'kecamatan_id' => $kecamatanId,
                'desa_id' => Desa::where('nama', 'Batu Piring')->value('id'),
                'pegawai_ids' => [$pegawaiId, $pegawaiId],
            ])
            ->assertSessionHasErrors('pegawai_ids.1');

        $this->assertSame(0, Spt::count());
        $this->assertSame(0, Sppd::count());
    }

    public function test_update_duplicate_pegawai_ditolak(): void
    {
        $spt = $this->buatDataDasar();
        $sppd = $spt->sppds()->first();
        $pegawaiId = $spt->sppds()->value('pegawai_id');
        $kecamatanId = $spt->kecamatan_id;
        $jumlahSptSebelum = Spt::count();
        $jumlahSppdSebelum = Sppd::count();

        $this->actingAs($this->admin)
            ->put(route('form.update', $sppd), [
                'jenis_perjalanan' => 'Dalam Daerah',
                'tanggal_spt' => '2026-08-12',
                'tanggal_berangkat' => '2026-08-13',
                'tanggal_kembali' => '2026-08-15',
                'perihal' => 'Rapat koordinasi',
                'kecamatan_id' => $kecamatanId,
                'desa_id' => $spt->desa_id,
                'pegawai_ids' => [$pegawaiId, $pegawaiId],
            ])
            ->assertSessionHasErrors('pegawai_ids.1');

        $this->assertSame($jumlahSptSebelum, Spt::count());
        $this->assertSame($jumlahSppdSebelum, Sppd::count());
    }

    public function test_unique_spt_pegawai_menolak_duplikat_di_database(): void
    {
        $spt = $this->buatDataDasar();
        $baris = $spt->sppds()->firstOrFail();

        $this->expectException(\Illuminate\Database\QueryException::class);

        Sppd::create([
            'spt_id' => $baris->spt_id,
            'pegawai_id' => $baris->pegawai_id,
            'nomor_sppd' => '999.9/999/DISCOMINFOSAN-BLG/VIII/2026',
            'tanggal_berangkat' => '2026-08-13',
            'tanggal_kembali' => '2026-08-15',
        ]);
    }

    public function test_create_tanggal_spt_sebelum_berangkat_valid(): void
    {
        $kecamatanId = Desa::where('nama', 'Batu Piring')->value('kecamatan_id');
        $pegawaiId = Pegawai::orderBy('nama')->value('id');

        $this->actingAs($this->admin)
            ->post(route('form.simpan'), [
                'jenis_perjalanan' => 'Dalam Daerah',
                'tanggal_spt' => '2026-08-11',
                'tanggal_berangkat' => '2026-08-13',
                'tanggal_kembali' => '2026-08-15',
                'perihal' => 'Rapat koordinasi',
                'kecamatan_id' => $kecamatanId,
                'desa_id' => Desa::where('nama', 'Batu Piring')->value('id'),
                'pegawai_ids' => [$pegawaiId],
            ])
            ->assertRedirect(route('dalam-daerah'));

        $this->assertSame(1, Spt::count());
    }

    public function test_create_tanggal_spt_sama_dengan_berangkat_valid(): void
    {
        $kecamatanId = Desa::where('nama', 'Batu Piring')->value('kecamatan_id');
        $pegawaiId = Pegawai::orderBy('nama')->value('id');

        $this->actingAs($this->admin)
            ->post(route('form.simpan'), [
                'jenis_perjalanan' => 'Dalam Daerah',
                'tanggal_spt' => '2026-08-13',
                'tanggal_berangkat' => '2026-08-13',
                'tanggal_kembali' => '2026-08-15',
                'perihal' => 'Rapat koordinasi',
                'kecamatan_id' => $kecamatanId,
                'desa_id' => Desa::where('nama', 'Batu Piring')->value('id'),
                'pegawai_ids' => [$pegawaiId],
            ])
            ->assertRedirect(route('dalam-daerah'));

        $this->assertSame(1, Spt::count());
    }

    public function test_create_tanggal_spt_setelah_berangkat_ditolak(): void
    {
        $kecamatanId = Desa::where('nama', 'Batu Piring')->value('kecamatan_id');
        $pegawaiId = Pegawai::orderBy('nama')->value('id');

        $this->actingAs($this->admin)
            ->post(route('form.simpan'), [
                'jenis_perjalanan' => 'Dalam Daerah',
                'tanggal_spt' => '2026-08-14',
                'tanggal_berangkat' => '2026-08-13',
                'tanggal_kembali' => '2026-08-15',
                'perihal' => 'Rapat koordinasi',
                'kecamatan_id' => $kecamatanId,
                'desa_id' => Desa::where('nama', 'Batu Piring')->value('id'),
                'pegawai_ids' => [$pegawaiId],
            ])
            ->assertSessionHasErrors('tanggal_spt');

        $this->assertSame(0, Spt::count());
        $this->assertSame(0, Sppd::count());
    }

    public function test_create_perihal_valid_diterima(): void
    {
        $payload = $this->payloadDalamDaerah();
        $payload['perihal'] = str_repeat('a', 500);

        $this->actingAs($this->admin)
            ->post(route('form.simpan'), $payload)
            ->assertRedirect(route('dalam-daerah'));

        $this->assertSame(1, Spt::count());
    }

    public function test_create_perihal_melebihi_batas_ditolak(): void
    {
        $payload = $this->payloadDalamDaerah();
        $payload['perihal'] = str_repeat('a', 501);

        $this->actingAs($this->admin)
            ->post(route('form.simpan'), $payload)
            ->assertSessionHasErrors('perihal');

        $this->assertSame(0, Spt::count());
        $this->assertSame(0, Sppd::count());
    }

    public function test_create_dasar_valid_diterima(): void
    {
        $payload = $this->payloadDalamDaerah();
        $payload['dasar'] = str_repeat('b', 2000);

        $this->actingAs($this->admin)
            ->post(route('form.simpan'), $payload)
            ->assertRedirect(route('dalam-daerah'));

        $this->assertSame(1, Spt::count());
    }

    public function test_create_dasar_melebihi_batas_ditolak(): void
    {
        $payload = $this->payloadDalamDaerah();
        $payload['dasar'] = str_repeat('b', 2001);

        $this->actingAs($this->admin)
            ->post(route('form.simpan'), $payload)
            ->assertSessionHasErrors('dasar');

        $this->assertSame(0, Spt::count());
        $this->assertSame(0, Sppd::count());
    }

    public function test_create_desa_valid_menyimpan_id_dan_snapshot(): void
    {
        $kecamatanId = Desa::where('nama', 'Batu Piring')->value('kecamatan_id');
        $desaId = Desa::where('nama', 'Batu Piring')->value('id');
        $pegawaiId = Pegawai::orderBy('nama')->value('id');

        $this->actingAs($this->admin)
            ->post(route('form.simpan'), [
                'jenis_perjalanan' => 'Dalam Daerah',
                'tanggal_spt' => '2026-08-12',
                'tanggal_berangkat' => '2026-08-13',
                'tanggal_kembali' => '2026-08-15',
                'perihal' => 'Rapat koordinasi',
                'kecamatan_id' => $kecamatanId,
                'desa_id' => $desaId,
                'pegawai_ids' => [$pegawaiId],
            ])
            ->assertRedirect(route('dalam-daerah'));

        $spt = Spt::first();

        $this->assertSame($desaId, $spt->desa_id);
        $this->assertSame('Batu Piring', $spt->desa);
    }

    public function test_create_desa_kecamatan_lain_ditolak(): void
    {
        $awayan = Kecamatan::where('nama', 'Awayan')->value('id');
        $desaId = Desa::where('nama', 'Batu Piring')->value('id');
        $pegawaiId = Pegawai::orderBy('nama')->value('id');

        $this->actingAs($this->admin)
            ->post(route('form.simpan'), [
                'jenis_perjalanan' => 'Dalam Daerah',
                'tanggal_spt' => '2026-08-12',
                'tanggal_berangkat' => '2026-08-13',
                'tanggal_kembali' => '2026-08-15',
                'perihal' => 'Rapat koordinasi',
                'kecamatan_id' => $awayan,
                'desa_id' => $desaId,
                'pegawai_ids' => [$pegawaiId],
            ])
            ->assertSessionHasErrors('desa_id');

        $this->assertSame(0, Spt::count());
    }

    public function test_create_desa_id_tidak_ada_ditolak(): void
    {
        $kecamatanId = Desa::where('nama', 'Batu Piring')->value('kecamatan_id');
        $pegawaiId = Pegawai::orderBy('nama')->value('id');

        $this->actingAs($this->admin)
            ->post(route('form.simpan'), [
                'jenis_perjalanan' => 'Dalam Daerah',
                'tanggal_spt' => '2026-08-12',
                'tanggal_berangkat' => '2026-08-13',
                'tanggal_kembali' => '2026-08-15',
                'perihal' => 'Rapat koordinasi',
                'kecamatan_id' => $kecamatanId,
                'desa_id' => 999999,
                'pegawai_ids' => [$pegawaiId],
            ])
            ->assertSessionHasErrors('desa_id');

        $this->assertSame(0, Spt::count());
    }

    public function test_update_desa_mengubah_snapshot(): void
    {
        $spt = $this->buatDataDasar();
        $sppd = $spt->sppds()->first();
        $juai = Kecamatan::where('nama', 'Juai')->value('id');
        $galumbang = Desa::where('nama', 'Galumbang')->where('kecamatan_id', $juai)->value('id');

        $this->actingAs($this->admin)
            ->put(route('form.update', $sppd), [
                'jenis_perjalanan' => 'Dalam Daerah',
                'tanggal_spt' => '2026-08-12',
                'tanggal_berangkat' => '2026-08-13',
                'tanggal_kembali' => '2026-08-15',
                'perihal' => 'Rapat koordinasi',
                'kecamatan_id' => $juai,
                'desa_id' => $galumbang,
                'pegawai_ids' => $spt->sppds()->pluck('pegawai_id')->all(),
            ])
            ->assertRedirect(route('dalam-daerah'));

        $spt->refresh();

        $this->assertSame($galumbang, $spt->desa_id);
        $this->assertSame('Galumbang', $spt->desa);
    }

    public function test_edit_legacy_hydrate_desa_benar(): void
    {
        $spt = $this->buatDataDasar();
        DB::table('spts')->where('id', $spt->id)->update(['desa_id' => null]);
        $sppd = $spt->sppds()->first();

        $response = $this->actingAs($this->admin)->get(route('form.edit', $sppd));

        $response->assertOk();
        $this->assertSame(
            Spt::resolveDesaId($spt->desa, $spt->kecamatan_id),
            $response->viewData('selectedDesaId')
        );
        $this->assertNotNull($response->viewData('selectedDesaId'));
    }

    public function test_edit_legacy_mismatch_tanpa_tebakan(): void
    {
        $spt = $this->buatDataDasar();
        DB::table('spts')->where('id', $spt->id)->update(['desa_id' => null, 'desa' => 'Desa Fiktif']);
        $sppd = $spt->sppds()->first();

        $response = $this->actingAs($this->admin)->get(route('form.edit', $sppd));

        $response->assertOk();
        $this->assertNull($response->viewData('selectedDesaId'));
    }

    public function test_pdf_tetap_membaca_snapshot_desa(): void
    {
        $sumberSpt = file_get_contents(resource_path('views/pdf/spt.blade.php'));
        $sumberSppd = file_get_contents(resource_path('views/pdf/sppd.blade.php'));

        $this->assertStringContainsString('$spt->desa', $sumberSpt);
        $this->assertStringContainsString('$spt->desa', $sumberSppd);
        $this->assertStringNotContainsString('desa->nama', $sumberSpt.$sumberSppd);
    }

    public function test_create_tanpa_pegawai_ditolak(): void
    {
        $kecamatanId = Desa::where('nama', 'Batu Piring')->value('kecamatan_id');

        $this->actingAs($this->admin)
            ->post(route('form.simpan'), [
                'jenis_perjalanan' => 'Dalam Daerah',
                'tanggal_spt' => '2026-08-12',
                'tanggal_berangkat' => '2026-08-13',
                'tanggal_kembali' => '2026-08-15',
                'perihal' => 'Rapat',
                'kecamatan_id' => $kecamatanId,
                'desa_id' => Desa::where('nama', 'Batu Piring')->value('id'),
                'pegawai_ids' => [],
            ])
            ->assertSessionHasErrors('pegawai_ids');

        $this->assertSame(0, Spt::count());
    }

    public function test_edit_menampilkan_semua_pegawai_terpilih(): void
    {
        $spt = $this->buatDataDasar();
        $sppd = $spt->sppds()->first();

        $this->actingAs($this->admin)
            ->get(route('form.edit', $sppd))
            ->assertOk()
            ->assertSee('Edit SPT & SPPD')
            ->assertSee($spt->nomor_spt);
    }

    public function test_update_mengubah_spt_tanpa_mengubah_nomor_spt(): void
    {
        $spt = $this->buatDataDasar();
        $sppd = $spt->sppds()->first();
        $nomorSpt = $spt->nomor_spt;
        $pegawaiIds = Pegawai::orderBy('nama')->pluck('id')->take(2)->all();
        $kotaTujuanId = KotaTujuan::where('nama', 'Banjarmasin')->value('id');

        $this->actingAs($this->admin)
            ->put(route('form.update', $sppd), [
                'jenis_perjalanan' => 'Luar Daerah',
                'tanggal_spt' => '2026-08-12',
                'tanggal_berangkat' => '2026-08-16',
                'tanggal_kembali' => '2026-08-18',
                'perihal' => 'Bimtek Banjarmasin',
                'kota_tujuan_id' => $kotaTujuanId,
                'pegawai_ids' => $pegawaiIds,
            ])
            ->assertRedirect(route('luar-daerah'));

        $spt->refresh();

        $this->assertSame($nomorSpt, $spt->nomor_spt);
        $this->assertSame('Luar Daerah', $spt->jenis_perjalanan);
        $this->assertNull($spt->kecamatan_id);
        $this->assertNull($spt->desa);
        $this->assertSame($kotaTujuanId, $spt->kota_tujuan_id);
        $this->assertSame(2, $spt->sppds()->count());
    }

    public function test_delete_menghapus_spt_beserta_sppd(): void
    {
        $spt = $this->buatDataDasar();
        $sptId = $spt->id;
        $this->assertSame(3, $spt->sppds()->count());

        $this->actingAs($this->admin)
            ->delete(route('form.delete', $spt))
            ->assertRedirect()
            ->assertSessionHas('success', 'SPT dan seluruh SPPD terkait berhasil dihapus.');

        $this->assertNull(Spt::find($sptId));
        $this->assertSame(0, Sppd::where('spt_id', $sptId)->count());
    }

    public function test_delete_spt_tidak_menghapus_spt_lain(): void
    {
        $sptLain = $this->buatDataDasar();
        $pegawaiLain = Pegawai::whereNotIn('id', $sptLain->sppds()->pluck('pegawai_id'))->orderBy('nama')->firstOrFail();
        $kecamatanId = Kecamatan::where('nama', 'Awayan')->value('id');
        $desa = Desa::where('kecamatan_id', $kecamatanId)->orderBy('nama')->value('nama');

        $sptBaru = Spt::create([
            'jenis_perjalanan' => 'Dalam Daerah',
            'nomor_spt' => Spt::generateNomorSpt('2026-08-12'),
            'tanggal_spt' => '2026-08-12',
            'tanggal_berangkat' => '2026-08-13',
            'tanggal_kembali' => '2026-08-15',
            'perihal' => 'Rapat lain',
            'kecamatan_id' => $kecamatanId,
            'desa' => $desa,
            'kota_tujuan_id' => null,
        ]);

        Sppd::create([
            'spt_id' => $sptBaru->id,
            'pegawai_id' => $pegawaiLain->id,
            'nomor_sppd' => $pegawaiLain->kode_sppd.'/900/DISCOMINFOSAN-BLG/VIII/2026',
            'tanggal_berangkat' => '2026-08-13',
            'tanggal_kembali' => '2026-08-15',
        ]);

        $sptLainId = $sptLain->id;
        $sptBaruId = $sptBaru->id;

        $this->actingAs($this->admin)
            ->delete(route('form.delete', $sptLain))
            ->assertRedirect();

        $this->assertNull(Spt::find($sptLainId));
        $this->assertSame(0, Sppd::where('spt_id', $sptLainId)->count());
        $this->assertNotNull(Spt::find($sptBaruId));
        $this->assertSame(1, Sppd::where('spt_id', $sptBaruId)->count());
    }

    public function test_delete_spt_ditolak_untuk_non_admin(): void
    {
        $spt = $this->buatDataDasar();
        $nonAdmin = User::factory()->create(['role' => UserRole::User]);

        $this->actingAs($nonAdmin)
            ->delete(route('form.delete', $spt))
            ->assertForbidden();

        $this->assertNotNull(Spt::find($spt->id));
        $this->assertSame(3, Sppd::where('spt_id', $spt->id)->count());
    }

    public function test_halaman_daftar_menampilkan_data(): void
    {
        $this->buatDataDasar();

        $this->actingAs($this->admin)
            ->get(route('dalam-daerah'))
            ->assertOk()
            ->assertSee('Awayan');

        $this->actingAs($this->admin)
            ->get(route('luar-daerah'))
            ->assertOk();
    }

    protected function payloadDalamDaerah(): array
    {
        return [
            'jenis_perjalanan' => 'Dalam Daerah',
            'tanggal_spt' => '2026-08-12',
            'tanggal_berangkat' => '2026-08-13',
            'tanggal_kembali' => '2026-08-15',
            'perihal' => 'Rapat koordinasi',
            'kecamatan_id' => Desa::where('nama', 'Batu Piring')->value('kecamatan_id'),
            'desa_id' => Desa::where('nama', 'Batu Piring')->value('id'),
            'pegawai_ids' => [Pegawai::orderBy('nama')->value('id')],
        ];
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

        foreach (Pegawai::orderBy('nama')->limit(3)->get() as $i => $pegawai) {
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
