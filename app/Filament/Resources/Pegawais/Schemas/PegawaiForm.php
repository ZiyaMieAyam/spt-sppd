<?php

namespace App\Filament\Resources\Pegawais\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PegawaiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('nip')
                    ->label('NIP')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('nama')
                    ->label('Nama Pegawai')
                    ->required()
                    ->maxLength(255),

                TextInput::make('pangkat')
                    ->required()
                    ->maxLength(255),

                TextInput::make('golongan')
                    ->required()
                    ->maxLength(255),

                TextInput::make('jabatan')
                    ->required()
                    ->maxLength(255),

                TextInput::make('unit_kerja')
                    ->label('Unit Kerja')
                    ->required()
                    ->maxLength(255),

                Select::make('status')
                    ->options([
                        'ASN' => 'ASN',
                        'Non ASN' => 'Non ASN',
                    ])
                    ->default('ASN')
                    ->required()
                    ->native(false),
            ]);
    }
}