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
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SptSppdIntegrationTest extends TestCase
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

    protected function kecamatanDesa(string $namaDesa): array
    {
        $kecamatanId = Desa::where('nama', $namaDesa)->value('kecamatan_id');

        return [$kecamatanId, $namaDesa];
    }

    protected function desaKecamatan(string $namaKecamatan): array
    {
        $kecamatanId = Kecamatan::where('nama', $namaKecamatan)->value('id');
        $desa = Desa::where('kecamatan_id', $kecamatanId)->orderBy('nama')->value('nama');

        return [$kecamatanId, $desa];
    }

    protected function kotaTujuan(string $nama = 'Banjarmasin'): int
    {
        return KotaTujuan::where('nama', $nama)->value('id');
    }

    // ── Case 1 ───────────────────────────────────────────────
    // User membuat 1 SPT dengan 3 pegawai:
    // 1 record spts, 3 record sppds, spt_id sama, 3 pegawai_id berbeda

    public function test_case_1_user_creates_1_spt_with_3_pegawai(): void
    {
        $pegawaiIds = Pegawai::orderBy('nama')->pluck('id')->take(3)->all();
        [$kecamatanId, $desa] = $this->kecamatanDesa('Batu Piring');

        $this->actingAs($this->admin)
            ->post(route('form.simpan'), [
                'jenis_perjalanan' => 'Dalam Daerah',
                'tanggal_spt' => '2026-08-19',
                'tanggal_berangkat' => '2026-08-20',
                'tanggal_kembali' => '2026-08-22',
                'perihal' => 'Rapat integrasi data',
                'kecamatan_id' => $kecamatanId,
                'desa' => $desa,
                'pegawai_ids' => $pegawaiIds,
            ])
            ->assertRedirect(route('dalam-daerah'));

        // 1 SPT
        $this->assertSame(1, Spt::count());

        // 3 SPPD
        $this->assertSame(3, Sppd::count());

        $spt = Spt::first();

        // Semua SPPD punya spt_id yang sama
        $this->assertTrue(
            Sppd::where('spt_id', $spt->id)->count() === 3
        );

        // 3 pegawai_id berbeda
        $pegawaisOnSppd = Sppd::where('spt_id', $spt->id)
            ->pluck('pegawai_id')
            ->unique();
        $this->assertCount(3, $pegawaisOnSppd);

        // Semua pegawai_id yang dikirim ada di SPPD
        $this->assertEqualsCanonicalizing(
            $pegawaiIds,
            Sppd::where('spt_id', $spt->id)->pluck('pegawai_id')->toArray()
        );

        // Tanggal terisi di semua SPPD
        foreach ($spt->sppds as $sppd) {
            $this->assertNotNull($sppd->tanggal_berangkat);
            $this->assertNotNull($sppd->tanggal_kembali);
            $this->assertSame('2026-08-20', $sppd->tanggal_berangkat->format('Y-m-d'));
            $this->assertSame('2026-08-22', $sppd->tanggal_kembali->format('Y-m-d'));
        }
    }

    // ── Case 2 & 3 ──────────────────────────────────────────
    // Admin dan User menggunakan database yang sama.
    // Data yang dibuat User terlihat di Admin, dan sebaliknya.

    public function test_case_2_3_admin_and_user_share_same_database(): void
    {
        $pegawaiIds = Pegawai::orderBy('nama')->pluck('id')->take(2)->all();
        [$kecamatanId, $desa] = $this->kecamatanDesa('Batu Piring');

        // 1) User membuat SPT via FormController
        $this->actingAs($this->admin)
            ->post(route('form.simpan'), [
                'jenis_perjalanan' => 'Dalam Daerah',
                'tanggal_spt' => '2026-08-19',
                'tanggal_berangkat' => '2026-08-20',
                'tanggal_kembali' => '2026-08-22',
                'perihal' => 'User membuat ini',
                'kecamatan_id' => $kecamatanId,
                'desa' => $desa,
                'pegawai_ids' => $pegawaiIds,
            ])
            ->assertRedirect();

        $sptFromUser = Spt::first();
        $this->assertSame('User membuat ini', $sptFromUser->perihal);

        // Simulasikan Admin (Filament) membaca data yang sama:
        $sptSeenByAdmin = Spt::find($sptFromUser->id);
        $this->assertNotNull($sptSeenByAdmin);
        $this->assertSame($sptFromUser->nomor_spt, $sptSeenByAdmin->nomor_spt);

        // 2) Admin membuat SPT baru (simulasi Filament CreateSpt)
        $pegawai = Pegawai::orderBy('nama')->first();
        $nomorBaru = Spt::generateNomorSpt('2026-08-19');

        $sptAdmin = Spt::create([
            'jenis_perjalanan' => 'Luar Daerah',
            'nomor_spt' => $nomorBaru,
            'tanggal_spt' => '2026-08-19',
            'tanggal_berangkat' => '2026-08-25',
            'tanggal_kembali' => '2026-08-27',
            'perihal' => 'Admin membuat ini',
            'kota_tujuan_id' => $this->kotaTujuan(),
        ]);

        Sppd::create([
            'spt_id' => $sptAdmin->id,
            'pegawai_id' => $pegawai->id,
            'nomor_sppd' => Sppd::generateNomorSppd($pegawai->kode_sppd),
            'tanggal_berangkat' => '2026-08-25',
            'tanggal_kembali' => '2026-08-27',
        ]);

        // Simulasikan User melihat data yang dibuat Admin:
        $sptSeenByUser = Spt::find($sptAdmin->id);
        $this->assertNotNull($sptSeenByUser);
        $this->assertSame('Admin membuat ini', $sptSeenByUser->perihal);

        // Total: 2 SPT, 3 SPPD (2 dari user + 1 dari admin)
        $this->assertSame(2, Spt::count());
        $this->assertSame(3, Sppd::count());
    }

    // ── Case 4 ──────────────────────────────────────────────
    // Nomor SPT 001–004 tampil urut ascending (001 di atas)

    public function test_case_4_ordering_spt_is_ascending(): void
    {
        $pegawais = Pegawai::orderBy('nama')->take(1)->get();
        [$kecamatanId, $desa] = $this->desaKecamatan('Awayan');

        for ($i = 1; $i <= 4; $i++) {
            // Rentang sengaja sama: overlap BOLEH, tidak ada penolakan.
            $this->actingAs($this->admin)
                ->post(route('form.simpan'), [
                    'jenis_perjalanan' => 'Dalam Daerah',
                    'tanggal_spt' => '2026-08-19',
                    'tanggal_berangkat' => '2026-08-20',
                    'tanggal_kembali' => '2026-08-22',
                    'perihal' => "Perjalanan ke-$i",
                    'kecamatan_id' => $kecamatanId,
                    'desa' => $desa,
                    'pegawai_ids' => $pegawais->pluck('id')->toArray(),
                ])
                ->assertRedirect();
        }

        $this->assertSame(4, Spt::count());

        // Query persis seperti routes/web.php (cek-data):
        $spts = Spt::orderBy('id', 'asc')->get();

        $this->assertCount(4, $spts);

        // Nomor harus berurutan: 001, 002, 003, 004
        for ($i = 0; $i < 4; $i++) {
            $expectedNomor = sprintf(
                '090/%03d/SPT/DISKOMINFOSAN-BLG/VIII/2026',
                $i + 1
            );
            $this->assertSame(
                $expectedNomor,
                $spts[$i]->nomor_spt,
                "SPT ke-{$i}: expected $expectedNomor, got {$spts[$i]->nomor_spt}"
            );
        }

        // Verifikasi: id juga berurutan (ascending = nomor ascending).
        // ID absolut tidak diasumsikan karena auto-increment MySQL tidak
        // di-reset oleh rollback antar test; yang penting 4 id berurutan.
        $ids = $spts->pluck('id')->toArray();
        $this->assertSame($ids, range($ids[0], $ids[0] + 3));
    }

    // ── Case 4 tambahan: SPPD ordering ──────────────────────

    public function test_case_4_ordering_sppd_is_ascending_grouped_by_spt(): void
    {
        $pegawais = Pegawai::orderBy('nama')->take(2)->pluck('id')->toArray();
        [$kecamatanId1, $desa1] = $this->desaKecamatan('Awayan');
        [$kecamatanId2, $desa2] = $this->desaKecamatan('Halong');

        // SPT 1: 2 pegawai
        $this->actingAs($this->admin)
            ->post(route('form.simpan'), [
                'jenis_perjalanan' => 'Dalam Daerah',
                'tanggal_spt' => '2026-08-19',
                'tanggal_berangkat' => '2026-08-20',
                'tanggal_kembali' => '2026-08-22',
                'perihal' => 'SPT pertama',
                'kecamatan_id' => $kecamatanId1,
                'desa' => $desa1,
                'pegawai_ids' => $pegawais,
            ]);

        // SPT 2: 2 pegawai, rentang sama (overlap BOLEH).
        $this->actingAs($this->admin)
            ->post(route('form.simpan'), [
                'jenis_perjalanan' => 'Dalam Daerah',
                'tanggal_spt' => '2026-08-19',
                'tanggal_berangkat' => '2026-08-20',
                'tanggal_kembali' => '2026-08-22',
                'perihal' => 'SPT kedua',
                'kecamatan_id' => $kecamatanId2,
                'desa' => $desa2,
                'pegawai_ids' => $pegawais,
            ]);

        // Query seperti routes/web.php (spt_id asc, id asc)
        $sppds = Sppd::orderBy('spt_id', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $this->assertCount(4, $sppds);

        // Dua SPPD pertama satu SPT, dua berikutnya SPT lain yang lebih baru.
        // ID absolut tidak diasumsikan karena auto-increment MySQL tidak
        // di-reset oleh rollback antar test.
        $this->assertSame($sppds[0]->spt_id, $sppds[1]->spt_id);
        $this->assertSame($sppds[2]->spt_id, $sppds[3]->spt_id);
        $this->assertGreaterThan($sppds[1]->spt_id, $sppds[2]->spt_id);

        // Dalam satu spt_id, id harus ascending
        $this->assertLessThan($sppds[1]->id, $sppds[0]->id);
    }

    // ── Case 5 ──────────────────────────────────────────────
    // Dalam Daerah: Kecamatan & Desa muncul, Kota Tujuan tidak

    public function test_case_5_dalam_daerah_fields(): void
    {
        [$kecamatanId, $desa] = $this->desaKecamatan('Awayan');

        $this->actingAs($this->admin)
            ->post(route('form.simpan'), [
                'jenis_perjalanan' => 'Dalam Daerah',
                'tanggal_spt' => '2026-08-19',
                'tanggal_berangkat' => '2026-08-20',
                'tanggal_kembali' => '2026-08-22',
                'perihal' => 'Dalam daerah test',
                'kecamatan_id' => $kecamatanId,
                'desa' => $desa,
                'pegawai_ids' => [Pegawai::first()->id],
            ]);

        $spt = Spt::first();

        $this->assertSame('Dalam Daerah', $spt->jenis_perjalanan);
        $this->assertNotNull($spt->kecamatan_id, 'kecamatan_id should be set for Dalam Daerah');
        $this->assertNotNull($spt->desa, 'desa should be set for Dalam Daerah');
        $this->assertNull($spt->kota_tujuan_id, 'kota_tujuan_id should be null for Dalam Daerah');
        $this->assertSame($kecamatanId, $spt->kecamatan_id);

        $kecamatan = Kecamatan::find($spt->kecamatan_id);
        $this->assertSame('Awayan', $kecamatan->nama);
    }

    // ── Case 6 ──────────────────────────────────────────────
    // Luar Daerah: Kota Tujuan muncul, Kecamatan & Desa tidak

    public function test_case_6_luar_daerah_fields(): void
    {
        $kotaTujuanId = $this->kotaTujuan('Banjarmasin');

        $this->actingAs($this->admin)
            ->post(route('form.simpan'), [
                'jenis_perjalanan' => 'Luar Daerah',
                'tanggal_spt' => '2026-08-19',
                'tanggal_berangkat' => '2026-08-20',
                'tanggal_kembali' => '2026-08-22',
                'perihal' => 'Luar daerah test',
                'kota_tujuan_id' => $kotaTujuanId,
                'pegawai_ids' => [Pegawai::first()->id],
            ]);

        $spt = Spt::first();

        $this->assertSame('Luar Daerah', $spt->jenis_perjalanan);
        $this->assertNotNull($spt->kota_tujuan_id, 'kota_tujuan_id should be set for Luar Daerah');
        $this->assertNull($spt->kecamatan_id, 'kecamatan_id should be null for Luar Daerah');
        $this->assertNull($spt->desa, 'desa should be null for Luar Daerah');
        $this->assertSame($kotaTujuanId, $spt->kota_tujuan_id);

        $kota = KotaTujuan::find($spt->kota_tujuan_id);
        $this->assertSame('Banjarmasin', $kota->nama);
    }

    // ── Case 7 ──────────────────────────────────────────────
    // Edit/delete dari User memengaruhi data yang sama di Admin

    public function test_case_7_user_edit_affects_same_data(): void
    {
        $pegawais = Pegawai::orderBy('nama')->pluck('id')->take(2)->toArray();
        [$kecamatanId, $desa] = $this->desaKecamatan('Awayan');
        $kotaTujuanId = KotaTujuan::orderBy('id')->skip(1)->value('id');

        // Buat SPT via User
        $this->actingAs($this->admin)
            ->post(route('form.simpan'), [
                'jenis_perjalanan' => 'Dalam Daerah',
                'tanggal_spt' => '2026-08-19',
                'tanggal_berangkat' => '2026-08-20',
                'tanggal_kembali' => '2026-08-22',
                'perihal' => 'Sebelum edit',
                'kecamatan_id' => $kecamatanId,
                'desa' => $desa,
                'pegawai_ids' => $pegawais,
            ]);

        $spt = Spt::first();
        $sppd = $spt->sppds()->first();

        // Edit via User (FormController)
        $this->actingAs($this->admin)
            ->put(route('form.update', $sppd), [
                'jenis_perjalanan' => 'Luar Daerah',
                'tanggal_spt' => '2026-08-19',
                'tanggal_berangkat' => '2026-08-25',
                'tanggal_kembali' => '2026-08-27',
                'perihal' => 'Sesudah edit',
                'kota_tujuan_id' => $kotaTujuanId,
                'pegawai_ids' => $pegawais,
            ])
            ->assertRedirect(route('luar-daerah'));

        // Verifikasi data yang sama berubah (Admin akan melihat hal yang sama)
        $spt->refresh();
        $this->assertSame('Sesudah edit', $spt->perihal);
        $this->assertSame('Luar Daerah', $spt->jenis_perjalanan);
        $this->assertNull($spt->kecamatan_id);
        $this->assertSame($kotaTujuanId, $spt->kota_tujuan_id);

        // 2 SPPD tetap ada (dihapus dan dibuat ulang)
        $this->assertSame(2, $spt->sppds()->count());

        // Delete via User
        $sptId = $spt->id;

        $this->actingAs($this->admin)
            ->delete(route('form.delete', $spt))
            ->assertRedirect();

        $this->assertNull(Spt::find($sptId));
        $this->assertSame(0, Sppd::where('spt_id', $sptId)->count());
    }

    // ── Case 8 ──────────────────────────────────────────────
    // Edit/delete dari Admin memengaruhi data yang sama di User

    public function test_case_8_admin_edit_affects_same_data(): void
    {
        $pegawai = Pegawai::orderBy('nama')->first();
        $kecamatanId = Kecamatan::where('nama', 'Awayan')->value('id');

        // Admin membuat SPT langsung via model
        $spt = Spt::create([
            'jenis_perjalanan' => 'Dalam Daerah',
            'nomor_spt' => Spt::generateNomorSpt('2026-08-19'),
            'tanggal_spt' => '2026-08-19',
            'tanggal_berangkat' => '2026-08-20',
            'tanggal_kembali' => '2026-08-22',
            'perihal' => 'Admin membuat ini',
            'kecamatan_id' => $kecamatanId,
            'desa' => 'Desa Admin',
        ]);

        Sppd::create([
            'spt_id' => $spt->id,
            'pegawai_id' => $pegawai->id,
            'nomor_sppd' => Sppd::generateNomorSppd($pegawai->kode_sppd),
            'tanggal_berangkat' => '2026-08-20',
            'tanggal_kembali' => '2026-08-22',
        ]);

        $sptId = $spt->id;

        // User melihat data yang sama — cek via dalam-daerah
        $this->actingAs($this->admin)
            ->get(route('dalam-daerah'))
            ->assertOk()
            ->assertSee('Desa Admin');

        // Admin mengedit — gunakan DB query langsung untuk memastikan persist
        DB::table('spts')
            ->where('id', $sptId)
            ->update([
                'perihal' => 'Admin mengubah ini',
                'desa' => 'Desa Baru',
            ]);

        // Model harus di-refresh
        $spt->refresh();
        $this->assertSame('Admin mengubah ini', $spt->perihal);
        $this->assertSame('Desa Baru', $spt->desa);

        // User melihat perubahan yang sama
        $this->actingAs($this->admin)
            ->get(route('dalam-daerah'))
            ->assertOk()
            ->assertSee('Desa Baru');

        // Admin menghapus
        Sppd::where('spt_id', $sptId)->delete();
        Spt::where('id', $sptId)->delete();

        // User tidak melihat data lagi
        $this->actingAs($this->admin)
            ->get(route('dalam-daerah'))
            ->assertOk()
            ->assertDontSee('Desa Baru');

        $this->assertNull(Spt::find($sptId));
        $this->assertSame(0, Sppd::where('spt_id', $sptId)->count());
    }

    // ── Bonus: Nomor SPPD memiliki tanggal yang benar ─────

    public function test_sppd_from_formcontroller_has_tanggal(): void
    {
        $pegawais = Pegawai::orderBy('nama')->pluck('id')->take(2)->toArray();
        [$kecamatanId, $desa] = $this->desaKecamatan('Awayan');

        $this->actingAs($this->admin)
            ->post(route('form.simpan'), [
                'jenis_perjalanan' => 'Dalam Daerah',
                'tanggal_spt' => '2026-08-19',
                'tanggal_berangkat' => '2026-08-20',
                'tanggal_kembali' => '2026-08-22',
                'perihal' => 'Tanggal test',
                'kecamatan_id' => $kecamatanId,
                'desa' => $desa,
                'pegawai_ids' => $pegawais,
            ]);

        foreach (Sppd::all() as $sppd) {
            $this->assertNotNull($sppd->tanggal_berangkat, 'SPPD tanggal_berangkat should not be null');
            $this->assertNotNull($sppd->tanggal_kembali, 'SPPD tanggal_kembali should not be null');
            $this->assertSame('2026-08-20', $sppd->tanggal_berangkat->format('Y-m-d'));
            $this->assertSame('2026-08-22', $sppd->tanggal_kembali->format('Y-m-d'));
        }
    }

    // ── Bonus: SPT dari FormController pakai tanggal_spt untuk generate ─

    public function test_generate_nomor_spt_uses_tanggal_from_form(): void
    {
        $pegawais = Pegawai::orderBy('nama')->pluck('id')->take(1)->toArray();
        [$kecamatanId, $desa] = $this->desaKecamatan('Awayan');

        // SPT pertama
        $this->actingAs($this->admin)
            ->post(route('form.simpan'), [
                'jenis_perjalanan' => 'Dalam Daerah',
                'tanggal_spt' => '2026-08-19',
                'tanggal_berangkat' => '2026-08-20',
                'tanggal_kembali' => '2026-08-22',
                'perihal' => 'Nomor test',
                'kecamatan_id' => $kecamatanId,
                'desa' => $desa,
                'pegawai_ids' => $pegawais,
            ]);

        $spt = Spt::first();
        $this->assertMatchesRegularExpression(
            '#^090/001/SPT/DISKOMINFOSAN-BLG/VIII/2026$#',
            $spt->nomor_spt
        );
    }

    // ── Bonus: Kecamatan & KotaTujuan nullable pada SPT ─────

    public function test_spt_dalam_daerah_has_kecamatan_and_desa(): void
    {
        [$kecamatanId, $desa] = $this->desaKecamatan('Halong');

        $this->actingAs($this->admin)
            ->post(route('form.simpan'), [
                'jenis_perjalanan' => 'Dalam Daerah',
                'tanggal_spt' => '2026-08-19',
                'tanggal_berangkat' => '2026-08-20',
                'tanggal_kembali' => '2026-08-22',
                'perihal' => 'Test relasi',
                'kecamatan_id' => $kecamatanId,
                'desa' => $desa,
                'pegawai_ids' => [Pegawai::first()->id],
            ]);

        $spt = Spt::first();

        $this->assertNotNull($spt->kecamatan);
        $this->assertSame('Halong', $spt->kecamatan->nama);
        $this->assertSame($desa, $spt->desa);
        $this->assertNull($spt->kotaTujuan);
    }
}
