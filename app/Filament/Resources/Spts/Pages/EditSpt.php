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
        $this->pegawaiIds = array_values(array_unique(array_map('intval', $data['pegawais'] ?? [])));

        unset($data['pegawais']);

        return $data;
    }

    protected function afterSave(): void
    {
        DB::transaction(function () {
            $pegawaiIds = $this->pegawaiIds;

            $existing = $this->record->sppds()->get()->keyBy('pegawai_id');

            // Hapus SPPD yang pegawainya tidak lagi ditugaskan.
            $this->record->sppds()->whereNotIn('pegawai_id', $pegawaiIds)->delete();

            // Pertahankan nomor_sppd pegawai yang tetap; hanya sinkronkan tanggal.
            foreach ($existing as $pegawaiId => $sppd) {
                if (in_array($pegawaiId, $pegawaiIds, true)) {
                    $sppd->update([
                        'tanggal_berangkat' => $this->record->tanggal_berangkat,
                        'tanggal_kembali' => $this->record->tanggal_kembali,
                    ]);
                }
            }

            // Buat SPPD hanya untuk pegawai baru agar nomor lama tidak berubah.
            $baruIds = array_values(array_diff($pegawaiIds, $existing->keys()->all()));

            if (! empty($baruIds)) {
                $tanggalSpt = Carbon::parse($this->record->tanggal_spt);
                $urutan = Sppd::nomorBerikutnya($tanggalSpt);

                foreach ($baruIds as $pegawaiId) {
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
