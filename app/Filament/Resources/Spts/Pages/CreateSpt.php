<?php

namespace App\Filament\Resources\Spts\Pages;

use App\Filament\Resources\Spts\SptResource;
use App\Models\Desa;
use App\Models\Spt;
use App\Services\SppdSyncService;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateSpt extends CreateRecord
{
    protected static string $resource = SptResource::class;

    protected array $pegawaiIds = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->pegawaiIds = array_values(array_unique(array_map('intval', $data['pegawais'] ?? [])));

        unset($data['pegawais']);

        $tanggalSpt = Carbon::parse($data['tanggal_spt']);
        $data['nomor_spt'] = Spt::generateNomorSpt($tanggalSpt);

        // Snapshot nama desa dari master terpilih (kolom string tetap sumber PDF).
        $data['desa'] = ! empty($data['desa_id'])
            ? Desa::whereKey($data['desa_id'])->value('nama')
            : null;

        return $data;
    }

    protected function afterCreate(): void
    {
        DB::transaction(function () {
            SppdSyncService::createForSpt(
                $this->record,
                $this->pegawaiIds,
                $this->record->tanggal_spt,
                $this->record->tanggal_berangkat,
                $this->record->tanggal_kembali,
                strict: false
            );
        });
    }
}
