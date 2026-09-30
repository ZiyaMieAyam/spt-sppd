<?php

namespace App\Console\Commands;

use App\Models\Spt;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillSptDesaId extends Command
{
    protected $signature = 'spt:backfill-desa-id';

    protected $description = 'Isi spts.desa_id dari spts.desa (cocok tepat nama + kecamatan_id). Tanpa fuzzy matching; yang tidak cocok dibiarkan NULL dan string desa tidak diubah.';

    public function handle(): int
    {
        $cocok = 0;
        $takCocok = 0;

        DB::table('spts')
            ->whereNull('desa_id')
            ->whereNotNull('desa')
            ->orderBy('id')
            ->chunkById(200, function ($rows) use (&$cocok, &$takCocok): void {
                foreach ($rows as $row) {
                    $desaId = Spt::resolveDesaId($row->desa, $row->kecamatan_id);

                    if ($desaId === null) {
                        $takCocok++;

                        continue;
                    }

                    DB::table('spts')->where('id', $row->id)->update(['desa_id' => $desaId]);
                    $cocok++;
                }
            });

        $this->info("desa_id terisi: {$cocok}, tidak cocok (tetap NULL): {$takCocok}.");

        return self::SUCCESS;
    }
}
