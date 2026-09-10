<?php

namespace App\Filament\Resources\Sppds\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class SppdInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextEntry::make('nomor_sppd')
                    ->label('Nomor SPPD'),

                TextEntry::make('spt.nomor_spt')
                    ->label('Nomor SPT'),

                TextEntry::make('pegawai.nama')
                    ->label('Pegawai'),

                TextEntry::make('tanggal_berangkat')
                    ->label('Tanggal Berangkat')
                    ->date(),

                TextEntry::make('tanggal_kembali')
                    ->label('Tanggal Kembali')
                    ->date(),

                TextEntry::make('created_at')
                    ->label('Dibuat')
                    ->dateTime(),

                TextEntry::make('updated_at')
                    ->label('Diubah')
                    ->dateTime(),
            ]);
    }
}
