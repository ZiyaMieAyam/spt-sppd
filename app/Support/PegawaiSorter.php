<?php

namespace App\Support;

use App\Models\Pegawai;
use Illuminate\Support\Collection;

class PegawaiSorter
{
    /**
     * Ranking golongan (semakin besar semakin tinggi).
     * Format di database konsisten "IV/c", tapi mapping dibuat
     * case-insensitive agar aman terhadap "III/A" dsb.
     */
    private const RANKING_GOLONGAN = [
        'I/A' => 1,
        'I/B' => 2,
        'I/C' => 3,
        'I/D' => 4,
        'I/E' => 5,
        'II/A' => 6,
        'II/B' => 7,
        'II/C' => 8,
        'II/D' => 9,
        'II/E' => 10,
        'III/A' => 11,
        'III/B' => 12,
        'III/C' => 13,
        'III/D' => 14,
        'III/E' => 15,
        'IV/A' => 16,
        'IV/B' => 17,
        'IV/C' => 18,
        'IV/D' => 19,
        'IV/E' => 20,
    ];

    public static function rankingGolongan(?string $golongan): int
    {
        if ($golongan === null) {
            return 0;
        }

        $golongan = strtoupper(trim($golongan));

        return self::RANKING_GOLONGAN[$golongan] ?? 0;
    }

    /**
     * Urutkan pegawai berdasarkan Pangkat/Golongan (tinggi ke rendah),
     * lalu Jabatan sebagai tie-breaker (a-z). usort pada PHP 8 stabil,
     * pegawai dengan golongan & jabatan sama mempertahankan urutan lama.
     *
     * @param iterable<Pegawai> $pegawais
     */
    public static function urutkan(iterable $pegawais): Collection
    {
        $items = collect($pegawais)->values()->all();

        usort($items, function (Pegawai $a, Pegawai $b): int {
            $selisih = self::rankingGolongan($b->golongan)
                <=> self::rankingGolongan($a->golongan);

            if ($selisih !== 0) {
                return $selisih;
            }

            return strnatcasecmp((string) $a->jabatan, (string) $b->jabatan);
        });

        return collect($items);
    }
}
