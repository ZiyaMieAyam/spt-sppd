<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Sppd extends Model
{
    protected $fillable = [
        'spt_id',
        'pegawai_id',
        'nomor_sppd',
        'tanggal_berangkat',
        'tanggal_kembali',
    ];

    protected $casts = [
        'tanggal_berangkat' => 'date',
        'tanggal_kembali' => 'date',
    ];

    public function spt()
    {
        return $this->belongsTo(Spt::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public static function generateNomorSppd(string $kode): string
    {
        $urutan = self::nomorBerikutnya();

        return self::formatNomorSppd($kode, $urutan);
    }

    public static function nomorBerikutnya(Carbon|string|null $tanggal = null): int
    {
        $tanggal = Carbon::parse($tanggal ?? now());

        $max = self::get('nomor_sppd')
            ->filter(function (self $item) use ($tanggal) {
                return str_ends_with($item->nomor_sppd, '/'.$tanggal->year);
            })
            ->map(function (self $item) {
                preg_match('/\/(\d+)\/DISKOMINFOSAN-BLG\//', $item->nomor_sppd, $match);

                return isset($match[1]) ? (int) $match[1] : 0;
            })
            ->max() ?? 0;

        return $max + 1;
    }

    public static function formatNomorSppd(
        string $kode,
        int $urutan,
        Carbon|string|null $tanggal = null
    ): string {
        $tanggal = Carbon::parse($tanggal ?? now());

        return sprintf(
            '%s/%03d/DISKOMINFOSAN-BLG/%s/%d',
            $kode,
            $urutan,
            Spt::bulanRomawi($tanggal->month),
            $tanggal->year
        );
    }
}
