<?php

namespace App\Filament\Resources\Spts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SptsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'asc')
            ->columns([

                TextColumn::make('nomor_spt')
                    ->label('Nomor SPT')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jenis_perjalanan')
                    ->label('Jenis Perjalanan')
                    ->badge()
                    ->sortable(),

                TextColumn::make('tanggal_spt')
                    ->label('Tanggal SPT')
                    ->date('d F Y')
                    ->sortable(),

                TextColumn::make('tanggal_berangkat')
                    ->label('Berangkat')
                    ->date('d F Y'),

                TextColumn::make('tanggal_kembali')
                    ->label('Kembali')
                    ->date('d F Y'),

                TextColumn::make('kecamatan.nama')
                    ->label('Kecamatan')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('kotaTujuan.nama')
                    ->label('Kota Tujuan')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('perihal')
                    ->label('Perihal')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->perihal),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}