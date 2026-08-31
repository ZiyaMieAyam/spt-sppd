<?php

namespace App\Filament\Resources\Spts\Schemas;

use App\Models\Pegawai;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SptForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([

                Select::make('jenis_perjalanan')
                    ->label('Jenis Perjalanan')
                    ->options([
                        'Dalam Daerah' => 'Dalam Daerah',
                        'Luar Daerah' => 'Luar Daerah',
                    ])
                    ->live()
                    ->required(),

                DatePicker::make('tanggal_spt')
                    ->label('Tanggal SPT')
                    ->required(),

                DatePicker::make('tanggal_berangkat')
                    ->label('Tanggal Berangkat')
                    ->required(),

                DatePicker::make('tanggal_kembali')
                    ->label('Tanggal Kembali')
                    ->required(),

                Textarea::make('perihal')
                    ->label('Perihal')
                    ->rows(3)
                    ->required()
                    ->columnSpanFull(),

                Textarea::make('dasar')
                    ->label('Dasar')
                    ->rows(3)
                    ->placeholder("Contoh:\n1. Peraturan Bupati Balangan Nomor ... Tahun ...\n2. Keputusan Bupati Balangan Nomor ... Tahun ...")
                    ->columnSpanFull(),

                Select::make('kecamatan_id')
                    ->label('Kecamatan')
                    ->relationship('kecamatan', 'nama')
                    ->searchable()
                    ->preload()
                    ->visible(fn ($get) => $get('jenis_perjalanan') === 'Dalam Daerah')
                    ->required(fn ($get) => $get('jenis_perjalanan') === 'Dalam Daerah'),

                Textarea::make('desa')
                    ->label('Desa')
                    ->rows(2)
                    ->visible(fn ($get) => $get('jenis_perjalanan') === 'Dalam Daerah')
                    ->required(fn ($get) => $get('jenis_perjalanan') === 'Dalam Daerah'),

                Select::make('kota_tujuan_id')
                    ->label('Kota Tujuan')
                    ->relationship('kotaTujuan', 'nama')
                    ->searchable()
                    ->preload()
                    ->visible(fn ($get) => $get('jenis_perjalanan') === 'Luar Daerah')
                    ->required(fn ($get) => $get('jenis_perjalanan') === 'Luar Daerah'),

                Select::make('pegawais')
                    ->label('Pegawai Yang Ditugaskan')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->options(
                        Pegawai::orderBy('nama')->pluck('nama', 'id')
                    )
                    ->afterStateHydrated(function (Select $component, ?\App\Models\Spt $record) {
                        if ($record) {
                            $component->state(
                                $record->pegawais()
                                    ->pluck('pegawais.id')
                                    ->toArray()
                            );
                        }
                    })
                    ->dehydrated()
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}