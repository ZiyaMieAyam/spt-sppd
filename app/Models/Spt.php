<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Spt extends Model
{
    protected $fillable = [
        'jenis_perjalanan',
        'nomor_spt',
        'tanggal_spt',
        'tanggal_berangkat',
        'tanggal_kembali',
        'perihal',
        'kecamatan_id',
        'desa',
        'kota_tujuan_id',
    ];

    protected $casts = [
        'tanggal_spt' => 'date',
        'tanggal_berangkat' => 'date',
        'tanggal_kembali' => 'date',
    ];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function kotaTujuan()
    {
        return $this->belongsTo(KotaTujuan::class);
    }

    public function sppds()
    {
        return $this->hasMany(Sppd::class);
    }

    public function pegawais()
    {
        return $this->belongsToMany(
            Pegawai::class,
            'sppds',
            'spt_id',
            'pegawai_id'
        );
    }

    public static function generateNomorSpt(Carbon|string|null $tanggal = null): string
    {
        $tanggal = Carbon::parse($tanggal ?? now());

        $nomor = self::get('nomor_spt')
            ->filter(function (self $item) use ($tanggal) {
                return str_ends_with($item->nomor_spt, '/'.$tanggal->year);
            })
            ->map(function (self $item) {
                preg_match('/^090\/(\d+)\//', $item->nomor_spt, $match);

                return isset($match[1]) ? (int) $match[1] : 0;
            })
            ->max() ?? 0;

        return sprintf(
            '090/%03d/SPT/DISKOMINFOSAN-BLG/%s/%d',
            $nomor + 1,
            self::bulanRomawi($tanggal->month),
            $tanggal->year
        );
    }

    public static function bulanRomawi(int $bulan): string
    {
        $romawi = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        return $romawi[$bulan] ?? '';
    }
}
