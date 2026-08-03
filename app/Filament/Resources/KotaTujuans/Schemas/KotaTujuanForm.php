<?php

namespace App\Filament\Resources\KotaTujuans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KotaTujuanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama Kota Tujuan')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
            ]);
    }
}