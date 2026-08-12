<?php

namespace App\Filament\Resources\Spts\Pages;

use App\Filament\Resources\Spts\SptResource;
use App\Models\Pegawai;
use App\Models\Sppd;
use App\Models\Spt;
use Filament\Resources\Pages\CreateRecord;

class CreateSpt extends CreateRecord
{
    protected static string $resource = SptResource::class;

    protected array $pegawaiIds = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->pegawaiIds = $data['pegawais'] ?? [];

        unset($data['pegawais']);

        $data['nomor_spt'] = Spt::generateNomorSpt();

        return $data;
    }

    protected function afterCreate(): void
    {
        foreach ($this->pegawaiIds as $pegawaiId) {

            $pegawai = Pegawai::find($pegawaiId);

            if (! $pegawai) {
                continue;
            }

            Sppd::create([
                'spt_id'      => $this->record->id,
                'pegawai_id'  => $pegawai->id,
                'nomor_sppd'  => Sppd::generateNomorSppd($pegawai->kode_sppd),
            ]);
        }
    }
}