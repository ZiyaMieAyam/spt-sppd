<?php

namespace App\Services;

use App\Models\Sppd;
use App\Models\Spt;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class ScheduleOverlapService
{
    /**
     * Logic inti overlap: dua range [A_start, A_end] dan [B_start, B_end] bentrok jika:
     *   A_start <= B_end && A_end >= B_start   (inklusif, batas hari dianggap bentrok)
     *
     * Contoh dengan existing 08-10:
     *   08-10 vs 10-12 = bentrok (10 <=10 && 12>=08)
     *   08-10 vs 09-09 = bentrok
     *   08-10 vs 07-08 = bentrok
     *   08-10 vs 11-13 = tidak (11>10)
     *   08-10 vs 05-07 = tidak (07<08)
     */

    /**
     * Cek bentrok global berdasarkan tabel SPT.
     * Mengembalikan SPT yang pertama kali bentrok atau null jika tidak ada.
     *
     * @param  string  $tanggalBerangkat  Y-m-d
     * @param  string  $tanggalKembali  Y-m-d
     * @param  int|null  $excludeSptId  ID SPT yang sedang diedit (agar tidak dianggap bentrok dengan dirinya sendiri)
     */
    public static function findConflictSpt(
        string $tanggalBerangkat,
        string $tanggalKembali,
        ?int $excludeSptId = null
    ): ?Spt {
        return Spt::query()
            ->where('tanggal_berangkat', '<=', $tanggalKembali)
            ->where('tanggal_kembali', '>=', $tanggalBerangkat)
            ->when($excludeSptId !== null, fn (Builder $q) => $q->where('id', '!=', $excludeSptId))
            ->orderBy('tanggal_berangkat')
            ->first();
    }

    /**
     * Cek bentrok per-pegawai berdasarkan tabel SPPD.
     * Berguna jika validasi ingin lebih granular per pegawai.
     * Mengembalikan SPPD yang bentrok atau null.
     *
     * @param  array<int>  $pegawaiIds
     */
    public static function findConflictSppdPerPegawai(
        string $tanggalBerangkat,
        string $tanggalKembali,
        array $pegawaiIds,
        ?int $excludeSptId = null,
        ?int $excludeSppdId = null
    ): ?Sppd {
        if (empty($pegawaiIds)) {
            return null;
        }

        return Sppd::query()
            ->whereIn('pegawai_id', $pegawaiIds)
            ->where('tanggal_berangkat', '<=', $tanggalKembali)
            ->where('tanggal_kembali', '>=', $tanggalBerangkat)
            ->when($excludeSptId !== null, fn (Builder $q) => $q->where('spt_id', '!=', $excludeSptId))
            ->when($excludeSppdId !== null, fn (Builder $q) => $q->where('id', '!=', $excludeSppdId))
            ->with(['pegawai', 'spt'])
            ->orderBy('tanggal_berangkat')
            ->first();
    }

    /**
     * Cek gabungan: prioritaskan bentrok per-pegawai jika pegawaiIds tersedia,
     * fallback ke bentrok global SPT.
     * Return array dengan keys: type, record, message.
     *
     * @return array{type: string, record: Spt|Sppd, message: string}|null
     */
    public static function findConflict(
        string $tanggalBerangkat,
        string $tanggalKembali,
        ?int $excludeSptId = null,
        ?array $pegawaiIds = null,
        ?int $excludeSppdId = null
    ): ?array {
        // Jika ada pegawaiIds, cek per-pegawai dulu agar pesan bisa menyebut pegawai
        if (! empty($pegawaiIds)) {
            $sppd = self::findConflictSppdPerPegawai(
                $tanggalBerangkat,
                $tanggalKembali,
                $pegawaiIds,
                $excludeSptId,
                $excludeSppdId
            );

            if ($sppd) {
                return [
                    'type' => 'sppd',
                    'record' => $sppd,
                    'message' => self::formatMessageFromSppd($sppd),
                ];
            }
        }

        $spt = self::findConflictSpt($tanggalBerangkat, $tanggalKembali, $excludeSptId);

        if ($spt) {
            return [
                'type' => 'spt',
                'record' => $spt,
                'message' => self::formatMessageFromSpt($spt),
            ];
        }

        return null;
    }

    /**
     * Format pesan bentrok dari SPT.
     * Contoh: "Jadwal perjalanan dinas bentrok dengan SPPD yang sudah ada pada tanggal 08 September 2026 s/d 10 September 2026."
     */
    public static function formatMessageFromSpt(Spt $spt): string
    {
        $mulai = self::formatTanggalIndo($spt->tanggal_berangkat);
        $selesai = self::formatTanggalIndo($spt->tanggal_kembali);

        return "Jadwal perjalanan dinas bentrok dengan SPPD yang sudah ada pada tanggal {$mulai} s/d {$selesai}.";
    }

    public static function formatMessageFromSppd(Sppd $sppd): string
    {
        // SPPD punya tanggal sendiri, fallback ke SPT jika null
        $berangkat = $sppd->tanggal_berangkat ?? $sppd->spt?->tanggal_berangkat;
        $kembali = $sppd->tanggal_kembali ?? $sppd->spt?->tanggal_kembali;

        $mulai = self::formatTanggalIndo($berangkat);
        $selesai = self::formatTanggalIndo($kembali);

        $pegawai = $sppd->pegawai?->nama ? " (pegawai: {$sppd->pegawai->nama})" : '';

        return "Jadwal perjalanan dinas bentrok dengan SPPD yang sudah ada pada tanggal {$mulai} s/d {$selesai}{$pegawai}.";
    }

    /**
     * Format tanggal ke Indonesia: 08 September 2026
     */
    public static function formatTanggalIndo(Carbon|string|null $tanggal): string
    {
        if (! $tanggal) {
            return '-';
        }

        $carbon = $tanggal instanceof Carbon ? $tanggal : Carbon::parse($tanggal);
        // translatedFormat butuh locale id
        $carbon->locale('id');

        return $carbon->translatedFormat('d F Y');
    }

    /**
     * Ambil jadwal perjalanan dinas terakhir berdasarkan tanggal_kembali paling akhir.
     * Sumber data: tabel spts (jadwal perjalanan dinas), bukan sekadar sppd per-pegawai.
     * SPPD mengikuti tanggal yang sama dari SPT, sehingga query SPT lebih representatif.
     */
    public static function getLastPerjalananDinas(?int $excludeSptId = null): ?Spt
    {
        return Spt::query()
            ->when($excludeSptId !== null, fn (Builder $q) => $q->where('id', '!=', $excludeSptId))
            ->orderByDesc('tanggal_kembali')
            ->orderByDesc('tanggal_berangkat')
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Ambil SPPD terakhir berdasarkan tanggal_kembali paling akhir.
     * Dipertahankan untuk kompatibilitas; untuk jadwal perjalanan dinas gunakan getLastPerjalananDinas().
     */
    public static function getLastSppd(?int $excludeSptId = null): ?Sppd
    {
        return Sppd::query()
            ->when($excludeSptId !== null, fn (Builder $q) => $q->where('spt_id', '!=', $excludeSptId))
            ->orderByDesc('tanggal_kembali')
            ->orderByDesc('tanggal_berangkat')
            ->orderByDesc('id')
            ->first();
    }

    /**
     * Tanggal tersedia berikutnya = tanggal_kembali jadwal terakhir + 1 hari.
     * Menerima Spt atau Sppd agar kompatibel dengan kedua sumber data.
     */
    public static function getNextAvailableDate(Spt|Sppd|null $last): ?Carbon
    {
        if (! $last || ! $last->tanggal_kembali) {
            return null;
        }

        $kembali = $last->tanggal_kembali instanceof Carbon
            ? $last->tanggal_kembali
            : Carbon::parse($last->tanggal_kembali);

        return $kembali->copy()->addDay();
    }

    /**
     * Helper untuk throw ValidationException dengan pesan bentrok di field tanggal_berangkat & tanggal_kembali.
     */
    public static function assertNoOverlap(
        string $tanggalBerangkat,
        string $tanggalKembali,
        ?int $excludeSptId = null,
        ?array $pegawaiIds = null,
        ?int $excludeSppdId = null
    ): void {
        $conflict = self::findConflict($tanggalBerangkat, $tanggalKembali, $excludeSptId, $pegawaiIds, $excludeSppdId);

        if ($conflict) {
            throw ValidationException::withMessages([
                'tanggal_berangkat' => $conflict['message'],
                'tanggal_kembali' => $conflict['message'],
            ]);
        }
    }
}
