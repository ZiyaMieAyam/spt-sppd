<?php

namespace App\Filament\Resources\Spts\Schemas;

use App\Models\Pegawai;
use App\Services\ScheduleOverlapService;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

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
                    ->required()
                    ->rules([
                        function (Get $get, ?Model $record): Closure {
                            return function (string $attribute, $value, Closure $fail) use ($get, $record) {
                                $berangkat = $value;
                                $kembali = $get('tanggal_kembali');

                                if (!$berangkat || !$kembali) {
                                    return;
                                }

                                // Normalisasi ke Y-m-d
                                $berangkatStr = $berangkat instanceof \DateTimeInterface ? $berangkat->format('Y-m-d') : (string) $berangkat;
                                $kembaliStr = $kembali instanceof \DateTimeInterface ? $kembali->format('Y-m-d') : (string) $kembali;

                                // Pastikan berangkat <= kembali (validasi lain sudah handle, tapi cegah false positive)
                                if ($berangkatStr > $kembaliStr) {
                                    return;
                                }

                                $excludeId = $record?->id;
                                $pegawaiIds = $get('pegawais') ?? [];

                                $conflict = ScheduleOverlapService::findConflict($berangkatStr, $kembaliStr, $excludeId, is_array($pegawaiIds) ? $pegawaiIds : []);

                                if ($conflict) {
                                    $fail($conflict['message']);
                                }
                            };
                        },
                    ]),

                DatePicker::make('tanggal_kembali')
                    ->label('Tanggal Kembali')
                    ->required()
                    ->afterOrEqual('tanggal_berangkat')
                    ->rules([
                        function (Get $get, ?Model $record): Closure {
                            return function (string $attribute, $value, Closure $fail) use ($get, $record) {
                                $kembali = $value;
                                $berangkat = $get('tanggal_berangkat');

                                if (!$berangkat || !$kembali) {
                                    return;
                                }

                                $berangkatStr = $berangkat instanceof \DateTimeInterface ? $berangkat->format('Y-m-d') : (string) $berangkat;
                                $kembaliStr = $kembali instanceof \DateTimeInterface ? $kembali->format('Y-m-d') : (string) $kembali;

                                if ($berangkatStr > $kembaliStr) {
                                    return;
                                }

                                $excludeId = $record?->id;
                                $pegawaiIds = $get('pegawais') ?? [];

                                $conflict = ScheduleOverlapService::findConflict($berangkatStr, $kembaliStr, $excludeId, is_array($pegawaiIds) ? $pegawaiIds : []);

                                if ($conflict) {
                                    $fail($conflict['message']);
                                }
                            };
                        },
                    ]),

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

                TextInput::make('tempat_kegiatan')
                    ->label('Tempat Kegiatan')
                    ->placeholder('Contoh: Hotel Aria Barito / Hotel Bandung / Aula Kecamatan')
                    ->maxLength(255)
                    ->visible(fn ($get) => in_array($get('jenis_perjalanan'), ['Dalam Daerah', 'Luar Daerah']))
                    ->columnSpanFull(),

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