<?php

namespace App\Filament\Resources\Sppds\Pages;

use App\Filament\Resources\Sppds\SppdResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSppd extends ViewRecord
{
    protected static string $resource = SppdResource::class;

    protected function getHeaderActions(): array
    {
        return [

            Action::make('cetakSppd')
                ->label('Cetak SPPD')
                ->icon('heroicon-o-printer')
                ->url(fn () => route('sppds.pdf', $this->record))
                ->openUrlInNewTab(),

            EditAction::make(),

        ];
    }
}
