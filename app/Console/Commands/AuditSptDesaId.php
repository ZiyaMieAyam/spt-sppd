<?php

namespace App\Console\Commands;

use App\Models\Spt;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AuditSptDesaId extends Command
{
    protected $signature = 'spt:audit-desa-id';

    protected $description = 'Audit READ-ONLY keterisian desa_id SPT (exact match nama + kecamatan_id). Tidak mengubah data apa pun.';

    public function handle(): int
    {
        $totalDesa = (int) DB::table('spts')->whereNotNull('desa')->count();
        $totalTerisi = (int) DB::table('spts')->whereNotNull('desa_id')->count();

        $mismatch = [];

        DB::table('spts')
            ->whereNotNull('desa')
            ->orderBy('id')
            ->chunkById(200, function ($rows) use (&$mismatch): void {
                $kecamatanIds = collect($rows)->pluck('kecamatan_id')->filter()->unique()->all();
                $kecamatanNama = DB::table('kecamatans')
                    ->whereIn('id', $kecamatanIds)
                    ->pluck('nama', 'id')
                    ->all();

                foreach ($rows as $row) {
                    // Baris yang desa_id-nya sudah terisi dianggap selesai.
                    if ($row->desa_id !== null) {
                        continue;
                    }

                    if (Spt::resolveDesaId($row->desa, $row->kecamatan_id) !== null) {
                        continue;
                    }

                    $mismatch[] = [
                        $row->id,
                        $row->nomor_spt,
                        $kecamatanNama[$row->kecamatan_id] ?? '-',
                        $row->desa,
                    ];
                }
            });

        $this->info("Total SPT dengan desa terisi : {$totalDesa}");
        $this->info("Total SPT dengan desa_id terisi: {$totalTerisi}");
        $this->info('Total mismatch (tanpa match)   : '.count($mismatch));

        if ($mismatch !== []) {
            $this->table(
                ['ID SPT', 'Nomor SPT', 'Kecamatan', 'Desa'],
                $mismatch
            );
        }

        $match = $totalDesa - count($mismatch);
        $this->info("Ringkasan: {$match} match / ".count($mismatch).' mismatch dari '.$totalDesa.' SPT berdesa.');

        return self::SUCCESS;
    }
}
