<?php

namespace App\Filament\Resources\Spts\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SppdsRelationManager extends RelationManager
{
    protected static string $relationship = 'sppds';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nomor_sppd')
            ->columns([
                TextColumn::make('nomor_sppd')
                    ->label('Nomor SPPD')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pegawai.nama')
                    ->label('Pegawai')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pegawai.nip')
                    ->label('NIP')
                    ->searchable(),

                TextColumn::make('pegawai.golongan')
                    ->label('Golongan')
                    ->sortable(),

                TextColumn::make('tanggal_berangkat')
                    ->label('Berangkat')
                    ->date('d M Y'),

                TextColumn::make('tanggal_kembali')
                    ->label('Kembali')
                    ->date('d M Y'),
            ])
            ->actions([
                Action::make('cetakSppd')
                    ->label('Cetak SPPD')
                    ->icon('heroicon-o-printer')
                    ->url(fn ($record) => route('sppds.pdf', $record))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
