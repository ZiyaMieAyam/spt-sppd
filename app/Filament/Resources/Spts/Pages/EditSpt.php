<?php

namespace App\Filament\Resources\Spts\Pages;

use App\Filament\Resources\Spts\SptResource;
use App\Models\Pegawai;
use App\Models\Sppd;
use Carbon\Carbon;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

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
        DB::transaction(function () {
            $this->record->sppds()->delete();

            $tanggalSpt = Carbon::parse($this->record->tanggal_spt);
            $urutan = Sppd::nomorBerikutnya($tanggalSpt);

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

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
