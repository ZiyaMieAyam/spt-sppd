<?php

namespace App\Filament\Resources\Sppds;

use App\Filament\Resources\Sppds\Pages\EditSppd;
use App\Filament\Resources\Sppds\Pages\ListSppds;
use App\Filament\Resources\Sppds\Pages\ViewSppd;
use App\Filament\Resources\Sppds\Schemas\SppdForm;
use App\Filament\Resources\Sppds\Schemas\SppdInfolist;
use App\Filament\Resources\Sppds\Tables\SppdsTable;
use App\Models\Sppd;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SppdResource extends Resource
{
    protected static ?string $model = Sppd::class;

    public static function form(Schema $schema): Schema
    {
        return SppdForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SppdInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SppdsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSppds::route('/'),
            'view' => ViewSppd::route('/{record}'),
            'edit' => EditSppd::route('/{record}/edit'),
        ];
    }
}
