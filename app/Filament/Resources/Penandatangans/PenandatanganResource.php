<?php

namespace App\Filament\Resources\Penandatangans;

use App\Filament\Resources\Penandatangans\Pages\CreatePenandatangan;
use App\Filament\Resources\Penandatangans\Pages\EditPenandatangan;
use App\Filament\Resources\Penandatangans\Pages\ListPenandatangans;
use App\Filament\Resources\Penandatangans\Schemas\PenandatanganForm;
use App\Filament\Resources\Penandatangans\Tables\PenandatangansTable;
use App\Models\Penandatangan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PenandatanganResource extends Resource
{
    protected static ?string $model = Penandatangan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static ?string $recordTitleAttribute = 'jabatan';

    protected static ?string $navigationLabel = 'Penandatangan';

    protected static ?string $pluralModelLabel = 'Penandatangan';

    protected static ?string $modelLabel = 'Penandatangan';

    public static function form(Schema $schema): Schema
    {
        return PenandatanganForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PenandatangansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPenandatangans::route('/'),
            'create' => CreatePenandatangan::route('/create'),
            'edit' => EditPenandatangan::route('/{record}/edit'),
        ];
    }
}
