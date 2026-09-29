<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class NomorUrut extends Model
{
    public const JENIS_SPT = 'spt';

    public const JENIS_SPPD = 'sppd';

    protected $fillable = [
        'jenis',
        'tahun',
        'nomor_terakhir',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'nomor_terakhir' => 'integer',
    ];

    /**
     * Reservasi nomor urut berikutnya secara aman terhadap race condition.
     *
     * Memakai transaksi sendiri + lockForUpdate() sehingga dua request
     * bersamaan tidak bisa mendapatkan nomor yang sama. Counter terpisah
     * per jenis + tahun (spt vs sppd tidak saling berebut nomor).
     *
     * Jika baris counter belum ada (tahun baru / migrasi awal), nilai awal
     * di-seed dari nomor terbesar yang sudah ada di tabel terkait agar
     * tidak menabrak penomoran lama, lalu dilanjutkan dari sana.
     *
     * @param  int  $jumlah  Berapa nomor yang direservasi sekaligus (SPPD butuh blok).
     * @return int Nomor pertama dari blok yang direservasi.
     */
    public static function reserveNext(string $jenis, int $tahun, int $jumlah = 1): int
    {
        $jumlah = max(1, $jumlah);

        return DB::transaction(function () use ($jenis, $tahun, $jumlah) {
            $row = self::query()
                ->where('jenis', $jenis)
                ->where('tahun', $tahun)
                ->lockForUpdate()
                ->first();

            if (! $row) {
                try {
                    $row = self::create([
                        'jenis' => $jenis,
                        'tahun' => $tahun,
                        'nomor_terakhir' => self::legacyMax($jenis, $tahun),
                    ]);
                } catch (QueryException $e) {
                    // Balapan dengan request lain yang membuat baris counter
                    // yang sama lebih dulu (unique jenis+tahun): pakai baris
                    // tersebut alih-alih gagal. Statement yang gagal tidak
                    // merusak transaksi InnoDB, jadi aman dilanjutkan.
                    $row = self::query()
                        ->where('jenis', $jenis)
                        ->where('tahun', $tahun)
                        ->lockForUpdate()
                        ->first();

                    if (! $row) {
                        throw $e;
                    }
                }
            }

            $pertama = $row->nomor_terakhir + 1;
            $row->increment('nomor_terakhir', $jumlah);

            return $pertama;
        });
    }

    /**
     * Nomor terbesar dari data lama (sebelum counter dipakai), per jenis + tahun.
     * Hanya dipanggil sekali saat seeding baris counter; setelah itu counter
     * menjadi satu-satunya sumber nomor. Query dibatasi per tahun + hanya
     * kolom nomor agar tidak memuat seluruh tabel ke memory.
     */
    protected static function legacyMax(string $jenis, int $tahun): int
    {
        if ($jenis === self::JENIS_SPT) {
            $max = 0;

            Spt::query()
                ->where('nomor_spt', 'like', '%/'.$tahun)
                ->pluck('nomor_spt')
                ->each(function (string $nomor) use (&$max) {
                    if (preg_match('/^090\/(\d+)\//', $nomor, $match)) {
                        $max = max($max, (int) $match[1]);
                    }
                });

            return $max;
        }

        $max = 0;

        Sppd::query()
            ->where('nomor_sppd', 'like', '%/'.$tahun)
            ->pluck('nomor_sppd')
            ->each(function (string $nomor) use (&$max) {
                if (preg_match('/\/(\d+)\/DISKOMINFOSAN-BLG\//', $nomor, $match)) {
                    $max = max($max, (int) $match[1]);
                }
            });

        return $max;
    }
}