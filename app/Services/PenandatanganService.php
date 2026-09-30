<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Models\Penandatangan;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PenandatanganService
{
    /**
     * Memo hasil barisDatabase() selama request ini saja.
     *
     * PHP-FPM: memori request terpisah, tidak ada risiko stale
     * lintas-request. Dipakai agar cari()/semua()/yangDiizinkan()/
     * valid() tidak mengulang query yang sama. Direset via
     * flushMemo() (dipakai test).
     *
     * @var array<string, array>|null
     */
    private static ?array $memoBaris = null;

    /**
     * Reset memo request. Dipakai test agar tidak bocor antar test
     * (PHPUnit berjalan dalam satu proses).
     */
    public static function flushMemo(): void
    {
        self::$memoBaris = null;
    }
    /**
     * Semua penandatangan yang terdaftar: kunci => definisi.
     *
     * Sumber utama adalah tabel `penandatangans` (dikelola via Filament);
     * config lama dipakai sebagai fallback bila tabel kosong/belum ada.
     * Hanya baris yang memiliki `kop` yang ditampilkan sebagai
     * pilihan penandatangan SPT (baris tanpa kop khusus untuk SPPD).
     *
     * @return array<string, array{jabatan: string, nama: ?string, nip: ?string, kop: string}>
     */
    public static function semua(): array
    {
        $db = self::barisDatabase();

        if ($db !== []) {
            return array_filter(
                $db,
                fn (array $definisi): bool => filled($definisi['kop'] ?? null)
            );
        }

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
     *
     * Urutan pencarian: pilihan SPT dari database, baris khusus
     * (mis. kepala_dinas, pejabat_teknis) dari database, lalu
     * config lama sebagai fallback.
     */
    public static function cari(?string $kunci): ?array
    {
        if ($kunci === null || $kunci === '') {
            return null;
        }

        // Satu kali fetch: turunan pilihan SPT (berkop) + baris khusus
        // (mis. kepala_dinas, pejabat_teknis) berasal dari hasil yang sama.
        $db = self::barisDatabase();

        foreach ($db as $k => $definisi) {
            if ($k === $kunci && filled($definisi['kop'] ?? null)) {
                return $definisi;
            }
        }

        if (isset($db[$kunci])) {
            return $db[$kunci];
        }

        $config = (array) Config::get('penandatangan.penandatangan', []);

        if (isset($config[$kunci])) {
            return $config[$kunci];
        }

        return null;
    }

    /**
     * Seluruh baris tabel penandatangans dalam format definisi,
     * dikunci berdasarkan kolom `kunci`. Mengembalikan array kosong
     * bila tabel belum ada atau belum berisi data.
     *
     * Hasil di-memo selama request agar tidak query berulang.
     *
     * @return array<string, array>
     */
    private static function barisDatabase(): array
    {
        if (self::$memoBaris !== null) {
            return self::$memoBaris;
        }

        try {
            $rows = Penandatangan::query()->orderBy('id')->get();
        } catch (QueryException $e) {
            Log::warning('PenandatanganService: gagal membaca tabel penandatangans, memakai config fallback.', [
                'error' => $e->getMessage(),
            ]);

            return self::$memoBaris = [];
        }

        if ($rows->isEmpty()) {
            return self::$memoBaris = [];
        }

        $hasil = [];

        foreach ($rows as $row) {
            $hasil[$row->kunci] = [
                'jabatan' => (string) $row->jabatan,
                'nama' => $row->nama,
                'nip' => $row->nip,
                'pangkat' => $row->pangkat,
                'golongan' => $row->golongan,
                'kop' => $row->kop,
            ];
        }

        return self::$memoBaris = $hasil;
    }

    /**
     * Penandatangan mana saja yang diizinkan untuk kombinasi jabatan
     * pegawai yang ditugaskan dalam sebuah SPT.
     *
     * Tanpa aturan di config => semua penandatangan diizinkan
     * (aman secara default).
     *
     * @param  iterable<Pegawai>  $pegawais
     * @return list<string>
     */
    public static function yangDiizinkan(iterable $pegawais): array
    {
        $aturan = Config::get('penandatangan.aturan_jabatan');

        if (! is_array($aturan) || $aturan === []) {
            return self::kunci();
        }

        $hasil = null;

        foreach ($pegawais as $pegawai) {
            foreach ($aturan as $rule) {
                if (! self::jabatanCocok((string) $pegawai->jabatan, $rule['cocok_jabatan'] ?? [])) {
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
     * @param  iterable<Pegawai>  $pegawais
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
