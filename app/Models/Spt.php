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
        'dasar',
        'kecamatan_id',
        'desa',
        'kota_tujuan_id',
        'tempat_kegiatan',
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

        // Reservasi via tabel nomor_uruts (counter 'spt' per tahun) dengan
        // lockForUpdate di transaksi sendiri, sehingga aman dari nomor
        // kembar saat dua request bersamaan. Format tidak berubah.
        $nomor = NomorUrut::reserveNext(NomorUrut::JENIS_SPT, $tanggal->year);

        return sprintf(
            '090/%03d/SPT/DISKOMINFOSAN-BLG/%s/%d',
            $nomor,
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
