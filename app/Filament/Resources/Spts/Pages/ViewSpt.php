<?php

namespace App\Filament\Resources\Spts\Pages;

use App\Filament\Resources\Spts\SptResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSpt extends ViewRecord
{
    protected static string $resource = SptResource::class;

    protected function getHeaderActions(): array
    {
        return [

            Action::make('cetakPdf')
                ->label('Cetak SPT')
                ->icon('heroicon-o-printer')
                ->url(fn () => route('spts.pdf', [
                    'spt' => $this->record,
                ]))
                ->openUrlInNewTab(),

            EditAction::make(),

        ];
    }
}