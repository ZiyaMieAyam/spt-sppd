<?php

namespace App\Filament\Resources\Sppds\Pages;

use App\Filament\Resources\Sppds\SppdResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSppd extends EditRecord
{
    protected static string $resource = SppdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
