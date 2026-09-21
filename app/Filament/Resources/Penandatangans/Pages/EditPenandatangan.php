<?php

namespace App\Filament\Resources\Penandatangans\Pages;

use App\Filament\Resources\Penandatangans\PenandatanganResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPenandatangan extends EditRecord
{
    protected static string $resource = PenandatanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
