<?php

namespace App\Filament\Resources\Spts\Pages;

use App\Filament\Resources\Spts\SptResource;
use App\Models\Pegawai;
use App\Models\Sppd;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSpt extends EditRecord
{
    protected static string $resource = SptResource::class;

    protected array $pegawaiIds = [];

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->pegawaiIds = $data['pegawais'] ?? [];

        unset($data['pegawais']);

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->sppds()->delete();

        foreach ($this->pegawaiIds as $pegawaiId) {

            $pegawai = Pegawai::find($pegawaiId);

            if (! $pegawai) {
                continue;
            }

            Sppd::create([
                'spt_id'     => $this->record->id,
                'pegawai_id' => $pegawai->id,
                'nomor_sppd' => Sppd::generateNomorSppd($pegawai->kode_sppd),
            ]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}