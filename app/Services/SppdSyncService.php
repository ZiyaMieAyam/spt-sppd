<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Models\Sppd;
use App\Models\Spt;
use Carbon\Carbon;

/**
 * Sinkronisasi SPPD milik satu SPT, dipakai bersama oleh FormController
 * dan halaman Filament (CreateSpt/EditSpt) agar behavior identik.
 *
 * Aturan:
 * - Pegawai yang masih ada: SPPD (id + nomor_sppd) dipertahankan,
 *   hanya tanggal berangkat/kembali disinkronkan.
 * - Pegawai yang dihapus: hanya SPPD pegawai tersebut yang dihapus.
 * - Pegawai baru: dibuatkan SPPD dengan nomor berikutnya dari counter.
 * - Tidak pernah membuat ulang semua SPPD; tidak menyentuh nomor SPT.
 */
class SppdSyncService
{
    /**
     * Normalisasi daftar pegawai: integer, unik, berurutan.
     *
     * @param  array<int|string>  $pegawaiIds
     * @return array<int>
     */
    public static function normalizePegawaiIds(array $pegawaiIds): array
    {
        return array_values(array_unique(array_map('intval', $pegawaiIds)));
    }

    /**
     * Buat SPPD untuk SPT baru. Satu blok nomor direservasi sekaligus
     * sehingga tidak ada celah race antar nomor dalam satu SPT.
     */
    public static function createForSpt(
        Spt $spt,
        array $pegawaiIds,
        Carbon|string $tanggalSpt,
        Carbon|string $tanggalBerangkat,
        Carbon|string $tanggalKembali,
        bool $strict = true
    ): void {
        $pegawaiIds = self::normalizePegawaiIds($pegawaiIds);

        if (empty($pegawaiIds)) {
            return;
        }

        $tanggalSpt = Carbon::parse($tanggalSpt);
        $urutan = Sppd::reserveNomorBlok(count($pegawaiIds), $tanggalSpt);

        foreach ($pegawaiIds as $pegawaiId) {
            $pegawai = $strict
                ? Pegawai::findOrFail($pegawaiId)
                : Pegawai::find($pegawaiId);

            if (! $pegawai) {
                continue;
            }

            Sppd::create([
                'spt_id' => $spt->id,
                'pegawai_id' => $pegawai->id,
                'nomor_sppd' => Sppd::formatNomorSppd(
                    $pegawai->kode_sppd,
                    $urutan,
                    $tanggalSpt
                ),
                'tanggal_berangkat' => $tanggalBerangkat,
                'tanggal_kembali' => $tanggalKembali,
            ]);

            $urutan++;
        }
    }

    /**
     * Sinkronisasi SPPD untuk SPT yang sudah ada berdasarkan selisih
     * daftar pegawai. Baris SPT sendiri tidak diubah di sini.
     *
     * @param  bool  $strict  true: pegawai hilang = exception (FormController);
     *                        false: pegawai hilang dilewati (Filament).
     */
    public static function syncForSpt(
        Spt $spt,
        array $pegawaiIds,
        Carbon|string $tanggalSpt,
        Carbon|string $tanggalBerangkat,
        Carbon|string $tanggalKembali,
        bool $strict = true
    ): void {
        $pegawaiIds = self::normalizePegawaiIds($pegawaiIds);

        $existing = $spt->sppds()->get()->keyBy('pegawai_id');

        // Hapus SPPD yang pegawainya tidak lagi ditugaskan.
        $spt->sppds()->whereNotIn('pegawai_id', $pegawaiIds)->delete();

        // Pertahankan SPPD pegawai yang tetap; hanya sinkronkan tanggal.
        foreach ($existing as $pegawaiId => $sppdRow) {
            if (in_array($pegawaiId, $pegawaiIds, true)) {
                $sppdRow->update([
                    'tanggal_berangkat' => $tanggalBerangkat,
                    'tanggal_kembali' => $tanggalKembali,
                ]);
            }
        }

        // Buat SPPD hanya untuk pegawai baru agar nomor lama tidak berubah.
        $baruIds = array_values(array_diff($pegawaiIds, $existing->keys()->all()));

        if (! empty($baruIds)) {
            self::createForSpt(
                $spt,
                $baruIds,
                $tanggalSpt,
                $tanggalBerangkat,
                $tanggalKembali,
                $strict
            );
        }
    }
}
