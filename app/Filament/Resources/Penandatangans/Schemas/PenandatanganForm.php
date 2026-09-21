<?php

namespace App\Filament\Resources\Penandatangans\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PenandatanganForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('kunci')
                    ->label('Kunci')
                    ->helperText('Kode unik penandatangan, dipakai sistem pada URL cetak PDF. Contoh: bupati, sekda, kepala_dinas.')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('jabatan')
                    ->label('Jabatan')
                    ->required()
                    ->maxLength(255),

                TextInput::make('nama')
                    ->label('Nama Pejabat')
                    ->nullable()
                    ->maxLength(255),

                TextInput::make('nip')
                    ->label('NIP')
                    ->nullable()
                    ->maxLength(255),

                TextInput::make('pangkat')
                    ->label('Pangkat')
                    ->nullable()
                    ->maxLength(255),

                TextInput::make('golongan')
                    ->label('Golongan')
                    ->nullable()
                    ->maxLength(255),

                Select::make('kop')
                    ->label('Kop Surat')
                    ->helperText('Jenis kop pada dokumen SPT. Kosongkan untuk penandatangan khusus SPPD.')
                    ->options([
                        'bupati' => 'Kop Bupati',
                        'sekda' => 'Kop Sekretariat Daerah',
                        'diskominfo' => 'Kop Diskominfo',
                    ])
                    ->nullable()
                    ->native(false),
            ]);
    }
}
