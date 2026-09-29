<?php

namespace App\Filament\Resources\Spts\Pages;

use App\Filament\Resources\Spts\SptResource;
use App\Services\SppdSyncService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditSpt extends EditRecord
{
    protected static string $resource = SptResource::class;

    protected array $pegawaiIds = [];

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->pegawaiIds = array_values(array_unique(array_map('intval', $data['pegawais'] ?? [])));

        unset($data['pegawais']);

        return $data;
    }

    protected function afterSave(): void
    {
        DB::transaction(function () {
            SppdSyncService::syncForSpt(
                $this->record,
                $this->pegawaiIds,
                $this->record->tanggal_spt,
                $this->record->tanggal_berangkat,
                $this->record->tanggal_kembali,
                strict: false
            );
        });
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
