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
                    ->label('Pangkat')
                    ->nullable()
                    ->maxLength(255)
                    ->placeholder('Kosongkan untuk PPPK (tidak ada pangkat)'),

                TextInput::make('golongan')
                    ->label('Golongan')
                    ->required()
                    ->maxLength(255),

                TextInput::make('jabatan')
                    ->label('Jabatan')
                    ->required()
                    ->maxLength(255),

                Select::make('kode_sppd')
                    ->label('Kode SPPD')
                    ->options([
                        '097.2' => '097.2 - Eselon I (Kepala Dinas)',
                        '097.3' => '097.3 - Eselon II (Sekretaris / Kabid)',
                        '097.4' => '097.4 - Eselon III (Kasi / Kasubbag)',
                        '097.5' => '097.5 - Staf',
                    ])
                    ->required()
                    ->native(false),

                TextInput::make('unit_kerja')
                    ->label('Unit Kerja')
                    ->required()
                    ->maxLength(255),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'ASN' => 'ASN',
                        'PPPK' => 'PPPK',
                        'Non ASN' => 'Non ASN',
                    ])
                    ->default('ASN')
                    ->required()
                    ->native(false),
            ]);
    }
}