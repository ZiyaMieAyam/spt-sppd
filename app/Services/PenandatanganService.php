<?php

namespace App\Services;

use App\Models\Pegawai;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class PenandatanganService
{
    /**
     * Semua penandatangan yang terdaftar: kunci => definisi.
     *
     * @return array<string, array{jabatan: string, nama: ?string, nip: ?string, kop: string}>
     */
    public static function semua(): array
    {
        return (array) Config::get('penandatangan.penandatangan', []);
    }

    /**
     * Kunci-kunci penandatangan (whitelist untuk validasi request).
     *
     * @return list<string>
     */
    public static function kunci(): array
    {
        return array_keys(self::semua());
    }

    /**
     * Ambil satu definisi penandatangan berdasarkan kuncinya.
     */
    public static function cari(?string $kunci): ?array
    {
        if ($kunci === null) {
            return null;
        }

        return self::semua()[$kunci] ?? null;
    }

    /**
     * Penandatangan mana saja yang diizinkan untuk kombinasi jabatan
     * pegawai yang ditugaskan dalam sebuah SPT.
     *
     * Tanpa aturan di config => semua penandatangan diizinkan
     * (aman secara default).
     *
     * @param iterable<Pegawai> $pegawais
     * @return list<string>
     */
    public static function yangDiizinkan(iterable $pegawais): array
    {
        $aturan = Config::get('penandatangan.aturan_jabatan');

        if (!is_array($aturan) || $aturan === []) {
            return self::kunci();
        }

        $hasil = null;

        foreach ($pegawais as $pegawai) {
            foreach ($aturan as $rule) {
                if (!self::jabatanCocok((string) $pegawai->jabatan, $rule['cocok_jabatan'] ?? [])) {
                    continue;
                }

                $boleh = array_values((array) ($rule['boleh'] ?? []));

                $hasil = $hasil === null
                    ? $boleh
                    : array_values(array_intersect($hasil, $boleh));

                break;
            }
        }

        return $hasil ?? self::kunci();
    }

    /**
     * Apakah pilihan penandatangan valid untuk pegawai-pegawai ini?
     *
     * @param iterable<Pegawai> $pegawais
     */
    public static function valid(string $kunci, iterable $pegawais): bool
    {
        return in_array($kunci, self::yangDiizinkan($pegawais), true);
    }

    /**
     * Path absolut gambar kop untuk jenis kop tertentu.
     */
    public static function pathKop(string $jenisKop): string
    {
        $relatif = Config::string(
            "penandatangan.kop.{$jenisKop}.gambar",
            ''
        );

        return public_path($relatif);
    }

    private static function jabatanCocok(string $jabatan, array $pola): bool
    {
        foreach ($pola as $satu) {
            if (fnmatch(Str::lower($satu), Str::lower($jabatan))) {
                return true;
            }
        }

        return false;
    }
}
