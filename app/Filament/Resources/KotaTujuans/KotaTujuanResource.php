<?php

namespace App\Filament\Resources\KotaTujuans;

use App\Filament\Resources\KotaTujuans\Pages\CreateKotaTujuan;
use App\Filament\Resources\KotaTujuans\Pages\EditKotaTujuan;
use App\Filament\Resources\KotaTujuans\Pages\ListKotaTujuans;
use App\Filament\Resources\KotaTujuans\Schemas\KotaTujuanForm;
use App\Filament\Resources\KotaTujuans\Tables\KotaTujuansTable;
use App\Models\KotaTujuan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KotaTujuanResource extends Resource
{
    protected static ?string $model = KotaTujuan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return KotaTujuanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KotaTujuansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKotaTujuans::route('/'),
            'create' => CreateKotaTujuan::route('/create'),
            'edit' => EditKotaTujuan::route('/{record}/edit'),
        ];
    }
}
