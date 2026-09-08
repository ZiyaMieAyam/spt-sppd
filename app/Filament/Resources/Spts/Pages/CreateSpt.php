<?php

namespace App\Filament\Resources\Spts\Pages;

use App\Filament\Resources\Spts\SptResource;
use App\Models\Pegawai;
use App\Models\Sppd;
use App\Models\Spt;
use App\Services\ScheduleOverlapService;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateSpt extends CreateRecord
{
    protected static string $resource = SptResource::class;

    protected array $pegawaiIds = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Validasi bentrok jadwal (backend, exclude null karena create baru)
        $berangkat = $data['tanggal_berangkat'] instanceof \DateTimeInterface
            ? $data['tanggal_berangkat']->format('Y-m-d')
            : (string) $data['tanggal_berangkat'];
        $kembali = $data['tanggal_kembali'] instanceof \DateTimeInterface
            ? $data['tanggal_kembali']->format('Y-m-d')
            : (string) $data['tanggal_kembali'];

        ScheduleOverlapService::assertNoOverlap(
            $berangkat,
            $kembali,
            null,
            $data['pegawais'] ?? []
        );

        $this->pegawaiIds = $data['pegawais'] ?? [];

        unset($data['pegawais']);

        $tanggalSpt = Carbon::parse($data['tanggal_spt']);
        $data['nomor_spt'] = Spt::generateNomorSpt($tanggalSpt);

        return $data;
    }

    protected function afterCreate(): void
    {
        DB::transaction(function () {
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
}