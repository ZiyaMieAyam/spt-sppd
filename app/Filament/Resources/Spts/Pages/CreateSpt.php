<?php

namespace App\Filament\Resources\Spts\Pages;

use App\Filament\Resources\Spts\SptResource;
use App\Models\Pegawai;
use App\Models\Sppd;
use App\Models\Spt;
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

        return $data;
    }

    protected function afterCreate(): void
    {
        DB::transaction(function () {
            $tanggalSpt = Carbon::parse($this->record->tanggal_spt);
            $urutan = Sppd::reserveNomorBlok(count($this->pegawaiIds), $tanggalSpt);

            foreach ($this->pegawaiIds as $pegawaiId) {
                $pegawai = Pegawai::find($pegawaiId);

                if (! $pegawai) {
                    continue;
                }

                Sppd::create([
                    'spt_id' => $this->record->id,
                    'pegawai_id' => $pegawai->id,
                    'nomor_sppd' => Sppd::formatNomorSppd(
                        $pegawai->kode_sppd,
                        $urutan,
                        $tanggalSpt
                    ),
                    'tanggal_berangkat' => $this->record->tanggal_berangkat,
                    'tanggal_kembali' => $this->record->tanggal_kembali,
                ]);

                $urutan++;
            }
        });
    }
}
