<?php

namespace App\Filament\Resources\Sppds\Pages;

use App\Filament\Resources\Sppds\SppdResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSppds extends ListRecords
{
    protected static string $resource = SppdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
