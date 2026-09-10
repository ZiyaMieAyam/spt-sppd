<?php

namespace App\Filament\Resources\Spts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SptInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Data Surat Perintah Tugas')
                    ->columns(2)
                    ->schema([

                        TextEntry::make('nomor_spt')
                            ->label('Nomor SPT'),

                        TextEntry::make('jenis_perjalanan')
                            ->label('Jenis Perjalanan')
                            ->badge(),

                        TextEntry::make('tanggal_spt')
                            ->label('Tanggal SPT')
                            ->date('d F Y'),

                        TextEntry::make('tanggal_berangkat')
                            ->label('Tanggal Berangkat')
                            ->date('d F Y'),

                        TextEntry::make('tanggal_kembali')
                            ->label('Tanggal Kembali')
                            ->date('d F Y'),

                        TextEntry::make('perihal')
                            ->label('Perihal')
                            ->columnSpanFull(),

                        TextEntry::make('dasar')
                            ->label('Dasar')
                            ->columnSpanFull(),

                        TextEntry::make('kecamatan.nama')
                            ->label('Kecamatan')
                            ->visible(fn ($record) => $record->jenis_perjalanan === 'Dalam Daerah'),

                        TextEntry::make('desa')
                            ->label('Desa')
                            ->visible(fn ($record) => $record->jenis_perjalanan === 'Dalam Daerah'),

                        TextEntry::make('kotaTujuan.nama')
                            ->label('Kota Tujuan')
                            ->visible(fn ($record) => $record->jenis_perjalanan === 'Luar Daerah'),

                        TextEntry::make('pegawai')
                            ->label('Pegawai Yang Ditugaskan')
                            ->state(function ($record) {
                                return $record->pegawais
                                    ->pluck('nama')
                                    ->implode(', ');
                            })
                            ->columnSpanFull(),

                    ]),
            ]);
    }
}
