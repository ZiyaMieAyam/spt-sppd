<?php

namespace App\Filament\Resources\Sppds\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SppdsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort(fn ($query) => $query
                ->orderBy('spt_id', 'asc')
                ->orderBy('id', 'asc')
            )
            ->columns([

                TextColumn::make('nomor_sppd')
                    ->label('Nomor SPPD')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('spt.nomor_spt')
                    ->label('Nomor SPT')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pegawai.nama')
                    ->label('Pegawai')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('spt.jenis_perjalanan')
                    ->label('Jenis')
                    ->badge()
                    ->sortable(),

                TextColumn::make('tujuan')
                    ->label('Tujuan')
                    ->getStateUsing(function ($record) {

                        if ($record->spt->jenis_perjalanan == 'Dalam Daerah') {
                            return $record->spt->kecamatan?->nama.' - '.$record->spt->desa;
                        }

                        return $record->spt->kotaTujuan?->nama;
                    })
                    ->wrap(),

                TextColumn::make('spt.tanggal_berangkat')
                    ->label('Berangkat')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('spt.tanggal_kembali')
                    ->label('Kembali')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([
                //
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
