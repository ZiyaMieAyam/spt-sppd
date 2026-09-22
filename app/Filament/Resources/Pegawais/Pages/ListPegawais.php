<?php

namespace App\Filament\Resources\Pegawais\Pages;

use App\Filament\Resources\Pegawais\PegawaiResource;
use App\Imports\PegawaiImport;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListPegawais extends ListRecords
{
    protected static string $resource = PegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadTemplate')
                ->label('Download Template Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->action(function () {
                    $path = storage_path('app/templates/Template_Data_Pegawai.xlsx');

                    if (! file_exists($path)) {
                        Notification::make()
                            ->title('Template tidak ditemukan.')
                            ->body('File Template_Data_Pegawai.xlsx tidak tersedia di server.')
                            ->danger()
                            ->send();

                        return;
                    }

                    return response()->download(
                        $path,
                        'Template_Data_Pegawai.xlsx',
                        ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
                    );
                }),
            Action::make('import')
                ->label('Import Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    FileUpload::make('file')
                        ->label('File Excel')
                        ->helperText('Unggah file .xlsx, .xls, atau .csv. Baris pertama wajib header: nip, nama, pangkat, golongan, jabatan, kode_sppd, unit_kerja, status.')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel',
                            'text/csv',
                            'text/plain',
                        ])
                        ->maxSize(10240)
                        ->storeFiles(false)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $import = new PegawaiImport;

                    Excel::import($import, $data['file']);

                    $failures = $import->failures();

                    if ($failures->isNotEmpty()) {
                        $lines = $failures->take(10)
                            ->map(fn ($failure): string => 'Baris '.$failure->row().': '.implode(' ', $failure->errors()))
                            ->implode("\n");

                        Notification::make()
                            ->title("Import selesai: {$import->importedRows} data masuk, {$failures->count()} baris gagal.")
                            ->body($lines)
                            ->warning()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title("Import berhasil: {$import->importedRows} data Pegawai masuk.")
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
