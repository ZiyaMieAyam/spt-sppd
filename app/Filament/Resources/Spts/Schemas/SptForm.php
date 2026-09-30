<?php

namespace App\Filament\Resources\Spts\Schemas;

use App\Models\Desa;
use App\Models\Pegawai;
use App\Models\Spt;
use App\Services\ScheduleOverlapService;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

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
                    ->required()
                    ->beforeOrEqual('tanggal_berangkat'),

                DatePicker::make('tanggal_berangkat')
                    ->label('Tanggal Berangkat')
                    ->required(),

                DatePicker::make('tanggal_kembali')
                    ->label('Tanggal Kembali')
                    ->required()
                    ->afterOrEqual('tanggal_berangkat'),

                Placeholder::make('sppd_last_info')
                    ->label('')
                    ->content(function (?Model $record): HtmlString {
                        $last = ScheduleOverlapService::getLastPerjalananDinas($record?->id);
                        if (! $last) {
                            return new HtmlString('<span style="font-size:11.5px;color:#6b7280;">Belum ada jadwal perjalanan dinas.</span>');
                        }
                        $fmtBerangkat = ScheduleOverlapService::formatTanggalIndo($last->tanggal_berangkat);
                        $fmtKembali = ScheduleOverlapService::formatTanggalIndo($last->tanggal_kembali);
                        $html = '<div style="font-size:11.5px;color:#6b7280;line-height:1.6;background:#f9fafb;border:1px solid #f3f4f6;border-radius:6px;padding:9px 12px;">';
                        $html .= '<span>Jadwal perjalanan dinas terakhir: '.e($fmtBerangkat).' s/d '.e($fmtKembali).'</span>';
                        $html .= '</div>';

                        return new HtmlString($html);
                    })
                    ->columnSpanFull(),

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
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('desa', null))
                    ->visible(fn ($get) => $get('jenis_perjalanan') === 'Dalam Daerah')
                    ->required(fn ($get) => $get('jenis_perjalanan') === 'Dalam Daerah'),

                Select::make('desa')
                    ->label('Desa/Kelurahan')
                    ->options(function (Get $get): array {
                        $kecamatanId = $get('kecamatan_id');
                        if (! $kecamatanId) {
                            return [];
                        }

                        return Desa::where('kecamatan_id', $kecamatanId)
                            ->orderBy('nama')
                            ->pluck('nama', 'nama')
                            ->toArray();
                    })
                    ->searchable()
                    ->preload()
                    ->live()
                    ->visible(fn ($get) => $get('jenis_perjalanan') === 'Dalam Daerah')
                    ->required(fn ($get) => $get('jenis_perjalanan') === 'Dalam Daerah')
                    ->placeholder('Pilih desa/kelurahan')
                    ->rules([
                        function (Get $get): Closure {
                            return function (string $attribute, $value, Closure $fail) use ($get) {
                                if (! $value) {
                                    return;
                                }
                                $kecamatanId = $get('kecamatan_id');
                                if (! $kecamatanId) {
                                    return;
                                }
                                $exists = Desa::where('nama', $value)
                                    ->where('kecamatan_id', $kecamatanId)
                                    ->exists();
                                if (! $exists) {
                                    $fail('Desa/Kelurahan tidak valid untuk kecamatan terpilih.');
                                }
                            };
                        },
                    ]),

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
                    ->afterStateHydrated(function (Select $component, ?Spt $record) {
                        if ($record) {
                            $component->state(
                                array_values(array_unique(
                                    $record->pegawais()
                                        ->pluck('pegawais.id')
                                        ->toArray()
                                ))
                            );
                        }
                    })
                    ->dehydrated()
                    ->required()
                    ->distinct()
                    ->validationMessages([
                        'distinct' => 'Pegawai tidak boleh dipilih lebih dari satu kali.',
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
