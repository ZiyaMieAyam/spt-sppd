<?php

namespace Tests\Feature;

use App\Models\Penandatangan;
use App\Services\PenandatanganService;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\Query\Grammars\MySqlGrammar;
use Illuminate\Database\Query\Processors\MySqlProcessor;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class PenandatanganServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        PenandatanganService::flushMemo();
    }

    protected function tearDown(): void
    {
        PenandatanganService::flushMemo();

        parent::tearDown();
    }

    public function test_tidak_query_berulang_dalam_satu_request(): void
    {
        DB::enableQueryLog();
        DB::flushQueryLog();

        PenandatanganService::semua();
        PenandatanganService::cari('sekda');
        PenandatanganService::cari('kepala_dinas');
        PenandatanganService::kunci();
        PenandatanganService::yangDiizinkan(collect());
        PenandatanganService::valid('sekda', collect());

        $this->assertSame(1, $this->hitungQueryPenandatangan());
    }

    public function test_cari_tidak_menyebabkan_query_kedua(): void
    {
        DB::enableQueryLog();
        DB::flushQueryLog();

        $pertama = PenandatanganService::cari('sekda');
        $kedua = PenandatanganService::cari('sekda');

        $this->assertSame($pertama, $kedua);
        $this->assertSame(1, $this->hitungQueryPenandatangan());
    }

    public function test_fallback_config_saat_tabel_kosong(): void
    {
        Penandatangan::query()->delete();
        PenandatanganService::flushMemo();

        $this->assertSame(
            (array) config('penandatangan.penandatangan'),
            PenandatanganService::semua()
        );
        $this->assertSame(
            config('penandatangan.penandatangan.sekda'),
            PenandatanganService::cari('sekda')
        );
    }

    public function test_fallback_config_saat_query_gagal_dan_log_warning(): void
    {
        $sebelum = PenandatanganService::semua();
        $this->assertNotSame([], $sebelum);

        $resolver = Model::getConnectionResolver();
        $qx = new QueryException(
            'mysql',
            'select * from `penandatangans` order by `id` asc',
            [],
            new \Exception('koneksi putus')
        );
        Model::setConnectionResolver($this->resolverGagal($qx));
        Log::spy();

        try {
            PenandatanganService::flushMemo();

            $this->assertSame(
                (array) config('penandatangan.penandatangan'),
                PenandatanganService::semua()
            );
            $this->assertSame(
                config('penandatangan.penandatangan.sekda'),
                PenandatanganService::cari('sekda')
            );

            Log::shouldHaveReceived('warning')->once();
        } finally {
            Model::setConnectionResolver($resolver);
            PenandatanganService::flushMemo();
        }
    }

    public function test_error_selain_queryexception_tidak_ditelan(): void
    {
        $resolver = Model::getConnectionResolver();
        Model::setConnectionResolver(new class implements ConnectionResolverInterface
        {
            public function connection($name = null)
            {
                throw new \RuntimeException('boom koneksi');
            }

            public function getDefaultConnection()
            {
                return 'mysql';
            }

            public function setDefaultConnection($name) {}
        });

        try {
            PenandatanganService::flushMemo();

            $this->expectException(\RuntimeException::class);

            PenandatanganService::cari('sekda');
        } finally {
            Model::setConnectionResolver($resolver);
            PenandatanganService::flushMemo();
        }
    }

    public function test_catch_hanya_queryexception_dengan_log(): void
    {
        $sumber = file_get_contents(app_path('Services/PenandatanganService.php'));

        $this->assertStringContainsString('catch (QueryException', $sumber);
        $this->assertStringContainsString('Log::warning', $sumber);
        $this->assertStringNotContainsString('catch (\\Throwable', $sumber);
    }

    protected function hitungQueryPenandatangan(): int
    {
        return collect(DB::getQueryLog())
            ->filter(fn (array $q): bool => str_contains($q['query'], 'penandatangans'))
            ->count();
    }

    protected function resolverGagal(QueryException $qx): ConnectionResolverInterface
    {
        $connection = Mockery::mock(MySqlConnection::class);
        $connection->shouldReceive('getTablePrefix')->andReturn('');
        $connection->shouldReceive('query')->andReturnUsing(
            fn (): \Illuminate\Database\Query\Builder => new \Illuminate\Database\Query\Builder(
                $connection,
                new MySqlGrammar($connection),
                new MySqlProcessor
            )
        );
        $connection->shouldReceive('select')->andThrow($qx);

        return new class($connection) implements ConnectionResolverInterface
        {
            public function __construct(private $connection) {}

            public function connection($name = null)
            {
                return $this->connection;
            }

            public function getDefaultConnection()
            {
                return 'mysql';
            }

            public function setDefaultConnection($name) {}
        };
    }
}
