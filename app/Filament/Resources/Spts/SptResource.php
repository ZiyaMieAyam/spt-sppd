<?php

namespace App\Filament\Resources\Spts;

use App\Filament\Resources\Spts\Pages\CreateSpt;
use App\Filament\Resources\Spts\Pages\EditSpt;
use App\Filament\Resources\Spts\Pages\ListSpts;
use App\Filament\Resources\Spts\Pages\ViewSpt;
use App\Filament\Resources\Spts\Schemas\SptForm;
use App\Filament\Resources\Spts\Schemas\SptInfolist;
use App\Filament\Resources\Spts\Tables\SptsTable;
use App\Models\Spt;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SptResource extends Resource
{
    protected static ?string $model = Spt::class;

    protected static ?string $navigationLabel = 'Surat Perintah Tugas';

    protected static ?string $modelLabel = 'SPT';

    protected static ?string $pluralModelLabel = 'Surat Perintah Tugas';

    protected static ?string $recordTitleAttribute = 'nomor_spt';

    public static function form(Schema $schema): Schema
    {
        return SptForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return SptInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SptsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSpts::route('/'),
            'create' => CreateSpt::route('/create'),
            'view' => ViewSpt::route('/{record}'),
            'edit' => EditSpt::route('/{record}/edit'),
        ];
    }
}