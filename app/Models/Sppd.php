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

    /**
     * Reservasi satu blok nomor SPPD sekaligus (satu SPT bisa untuk
     * beberapa pegawai). Mengembalikan nomor pertama; pemanggil
     * menaikkan +1 untuk tiap pegawai berikutnya tanpa query tambahan,
     * sehingga tidak ada celah race antar nomor dalam satu SPT.
     */
    public static function reserveNomorBlok(int $jumlah, Carbon|string|null $tanggal = null): int
    {
        $tanggal = Carbon::parse($tanggal ?? now());

        return NomorUrut::reserveNext(NomorUrut::JENIS_SPPD, $tanggal->year, $jumlah);
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
