<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Http\Controllers\PerjalananDinasController;
use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\Pegawai;
use App\Models\Spt;
use App\Models\User;
use App\Services\SppdSyncService;
use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PerjalananDinasTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->admin = User::factory()->create(['role' => UserRole::Admin]);
    }

    public function test_dalam_daerah_membutuhkan_auth(): void
    {
        $this->get(route('dalam-daerah'))->assertRedirect('/login');
        $this->get(route('luar-daerah'))->assertRedirect('/login');
    }

    public function test_dalam_daerah_bisa_diakses_admin_dengan_pagination(): void
    {
        $this->buatSptDalamDaerah(3);

        $response = $this->actingAs($this->admin)->get(route('dalam-daerah'));

        $response->assertOk();

        $data = $response->viewData('data');

        $this->assertInstanceOf(LengthAwarePaginator::class, $data);
        $this->assertSame(15, $data->perPage());
        $this->assertSame(3, $data->total());
    }

    public function test_luar_daerah_bisa_diakses_admin_dengan_pagination(): void
    {
        $response = $this->actingAs($this->admin)->get(route('luar-daerah'));

        $response->assertOk();

        $data = $response->viewData('data');

        $this->assertInstanceOf(LengthAwarePaginator::class, $data);
        $this->assertSame(15, $data->perPage());
    }

    public function test_pagination_halaman_kedua_dapat_diakses(): void
    {
        $this->buatSptDalamDaerah(16);

        $halaman1 = $this->actingAs($this->admin)->get(route('dalam-daerah'));
        $halaman1->assertOk();
        $this->assertSame(1, $halaman1->viewData('data')->currentPage());
        $this->assertSame(15, $halaman1->viewData('data')->count());

        $halaman2 = $this->actingAs($this->admin)->get(route('dalam-daerah', ['page' => 2]));
        $halaman2->assertOk();

        $data = $halaman2->viewData('data');

        $this->assertSame(2, $data->currentPage());
        $this->assertSame(1, $data->count());
        $this->assertSame(16, $data->firstItem());
    }

    public function test_route_tidak_menggunakan_closure(): void
    {
        foreach (['dalam-daerah', 'luar-daerah'] as $nama) {
            $route = Route::getRoutes()->getByName($nama);

            $this->assertNotNull($route);
            $this->assertNotInstanceOf(Closure::class, $route->getAction('uses'));
            $this->assertStringContainsString(
                PerjalananDinasController::class,
                is_string($route->getAction('uses')) ? $route->getAction('uses') : get_class($route->getAction('uses')),
            );
        }
    }

    protected function buatSptDalamDaerah(int $jumlah): void
    {
        $kecamatanId = Kecamatan::where('nama', 'Awayan')->value('id');
        $desa = Desa::where('kecamatan_id', $kecamatanId)->orderBy('nama')->value('nama');
        $pegawaiId = Pegawai::orderBy('nama')->value('id');

        for ($i = 1; $i <= $jumlah; $i++) {
            $spt = Spt::create([
                'jenis_perjalanan' => 'Dalam Daerah',
                'nomor_spt' => Spt::generateNomorSpt('2026-08-12'),
                'tanggal_spt' => '2026-08-12',
                'tanggal_berangkat' => '2026-08-13',
                'tanggal_kembali' => '2026-08-15',
                'perihal' => "Uji pagination {$i}",
                'kecamatan_id' => $kecamatanId,
                'desa' => $desa,
                'kota_tujuan_id' => null,
            ]);

            SppdSyncService::createForSpt(
                $spt,
                [$pegawaiId],
                '2026-08-12',
                '2026-08-13',
                '2026-08-15'
            );
        }
    }
}
