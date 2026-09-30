<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\Pegawai;
use App\Models\Spt;
use App\Models\User;
use App\Services\SppdSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_enum_mempertahankan_nilai_lama(): void
    {
        $this->assertSame('admin', UserRole::Admin->value);
        $this->assertSame('user', UserRole::User->value);
        $this->assertSame('Admin', UserRole::Admin->label());
        $this->assertSame('User', UserRole::User->label());
        $this->assertSame(
            ['admin' => 'Admin', 'user' => 'User'],
            UserRole::options()
        );
    }

    public function test_is_admin_membedakan_role(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $user = User::factory()->create(['role' => UserRole::User]);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($user->isAdmin());

        $this->assertSame(UserRole::Admin, $admin->fresh()->role);
        $this->assertSame(UserRole::User, $user->fresh()->role);
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'role' => 'admin']);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'role' => 'user']);
    }

    public function test_admin_mendapat_akses_user_ditolak(): void
    {
        $spt = $this->buatSpt();
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $user = User::factory()->create(['role' => UserRole::User]);

        $this->actingAs($admin)
            ->delete(route('form.delete', $spt))
            ->assertRedirect();

        $this->assertNull(Spt::find($spt->id));

        $spt = $this->buatSpt();

        $this->actingAs($user)
            ->delete(route('form.delete', $spt))
            ->assertForbidden();

        $this->assertNotNull(Spt::find($spt->id));
    }

    public function test_tidak_ada_perbandingan_string_role_mentah(): void
    {
        $userSrc = file_get_contents(app_path('Models/User.php'));
        $middlewareSrc = file_get_contents(app_path('Http/Middleware/EnsureRole.php'));

        $this->assertStringNotContainsString("=== 'admin'", $userSrc);
        $this->assertStringNotContainsString('in_array($user->role', $middlewareSrc);
        $this->assertStringContainsString('UserRole::Admin', $userSrc);
        $this->assertStringContainsString('UserRole', $middlewareSrc);
    }

    protected function buatSpt(): Spt
    {
        $kecamatanId = Kecamatan::where('nama', 'Awayan')->value('id');
        $desa = Desa::where('kecamatan_id', $kecamatanId)->orderBy('nama')->value('nama');

        $spt = Spt::create([
            'jenis_perjalanan' => 'Dalam Daerah',
            'nomor_spt' => Spt::generateNomorSpt('2026-08-12'),
            'tanggal_spt' => '2026-08-12',
            'tanggal_berangkat' => '2026-08-13',
            'tanggal_kembali' => '2026-08-15',
            'perihal' => 'Uji role',
            'kecamatan_id' => $kecamatanId,
            'desa' => $desa,
            'kota_tujuan_id' => null,
        ]);

        SppdSyncService::createForSpt(
            $spt,
            [Pegawai::orderBy('nama')->value('id')],
            '2026-08-12',
            '2026-08-13',
            '2026-08-15'
        );

        return $spt;
    }
}
