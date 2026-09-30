<?php

namespace Tests\Feature;

use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\KotaTujuan;
use App\Models\Pegawai;
use App\Models\Sppd;
use App\Models\Spt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $this->admin = User::factory()->create(['role' => 'admin']);
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
                'desa' => 'Batu Piring',
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
                'desa' => 'Batu Piring',
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
                'desa' => $spt->desa,
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
                'desa' => 'Batu Piring',
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
        $sppd = $spt->sppds()->first();
        $sptId = $spt->id;

        $this->actingAs($this->admin)
            ->delete(route('form.delete', $sppd))
            ->assertRedirect();

        $this->assertNull(Spt::find($sptId));
        $this->assertSame(0, Sppd::where('spt_id', $sptId)->count());
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
