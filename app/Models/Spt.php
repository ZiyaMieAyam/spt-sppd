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
        'desa_id',
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

    /**
     * Relasi master desa (tahap 1, opsional).
     *
     * Catatan: kolom string `desa` masih ada, sehingga akses properti
     * `$spt->desa` mengembalikan string historis. Relasi dipakai via
     * `$spt->desa()->...` atau `$spt->load('desa')` + `getRelation('desa')`,
     * atau via properti setelah kolom string dihapus di tahap berikutnya.
     */
    public function desa()
    {
        return $this->belongsTo(Desa::class, 'desa_id');
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

    protected static function booted(): void
    {
        // Tahap 1-3: jaga konsistensi pasangan desa_id + snapshot `desa`.
        // - String/kecamatan berubah (atau record baru): turunkan desa_id
        //   dari string via exact match (tidak mengubah string).
        // - String tidak berubah: jangan timpa desa_id eksplisit; hanya
        //   isi bila masih kosong (baris legacy yang disimpan ulang).
        static::saving(function (Spt $spt): void {
            if (! $spt->isDirty('desa') && ! $spt->isDirty('kecamatan_id')) {
                if (empty($spt->getAttributeFromArray('desa_id')) && $spt->desa && $spt->kecamatan_id) {
                    $spt->desa_id = self::resolveDesaId($spt->desa, (int) $spt->kecamatan_id);
                }

                return;
            }

            if ($spt->desa && $spt->kecamatan_id) {
                $spt->desa_id = self::resolveDesaId($spt->desa, (int) $spt->kecamatan_id);
            } elseif (! $spt->isDirty('desa_id')) {
                $spt->desa_id = null;
            }
        });
    }

    /**
     * Cari id master desa dengan pencocokan TEPAT (nama + kecamatan).
     * Tanpa fuzzy matching: tidak cocok => null.
     */
    public static function resolveDesaId(?string $nama, ?int $kecamatanId): ?int
    {
        if ($nama === null || $nama === '' || $kecamatanId === null) {
            return null;
        }

        return Desa::query()
            ->where('kecamatan_id', $kecamatanId)
            ->where('nama', $nama)
            ->value('id');
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
