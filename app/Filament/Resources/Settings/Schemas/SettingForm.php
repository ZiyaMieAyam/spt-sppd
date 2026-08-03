<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('kode_spt')
                    ->label('Kode SPT')
                    ->required()
                    ->maxLength(10),

                TextInput::make('nama_opd')
                    ->label('Nama OPD')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}