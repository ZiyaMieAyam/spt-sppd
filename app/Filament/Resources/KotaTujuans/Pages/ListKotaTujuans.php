<?php

namespace App\Filament\Resources\KotaTujuans\Pages;

use App\Filament\Resources\KotaTujuans\KotaTujuanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKotaTujuans extends ListRecords
{
    protected static string $resource = KotaTujuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
