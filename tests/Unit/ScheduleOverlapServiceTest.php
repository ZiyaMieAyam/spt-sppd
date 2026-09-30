<?php

namespace Tests\Unit;

use App\Services\ScheduleOverlapService;
use Carbon\Carbon;
use Tests\TestCase;

class ScheduleOverlapServiceTest extends TestCase
{
    public function test_format_tidak_memutasi_carbon_asli(): void
    {
        $asli = Carbon::parse('2026-09-08', 'UTC');
        $localeSemula = $asli->locale;
        $nilaiSemula = $asli->toDateTimeString();

        $hasil = ScheduleOverlapService::formatTanggalIndo($asli);

        $this->assertSame('08 September 2026', $hasil);
        $this->assertSame($nilaiSemula, $asli->toDateTimeString());
        $this->assertSame($localeSemula, $asli->locale);
    }

    public function test_format_mendukung_string_dan_null(): void
    {
        $this->assertSame('08 September 2026', ScheduleOverlapService::formatTanggalIndo('2026-09-08'));
        $this->assertSame('-', ScheduleOverlapService::formatTanggalIndo(null));
    }
}
