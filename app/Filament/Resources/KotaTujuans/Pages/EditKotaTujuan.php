<?php

namespace App\Filament\Resources\KotaTujuans\Pages;

use App\Filament\Resources\KotaTujuans\KotaTujuanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKotaTujuan extends EditRecord
{
    protected static string $resource = KotaTujuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
