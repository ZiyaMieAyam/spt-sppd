<?php

namespace App\Filament\Resources\Penandatangans\Pages;

use App\Filament\Resources\Penandatangans\PenandatanganResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPenandatangans extends ListRecords
{
    protected static string $resource = PenandatanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
