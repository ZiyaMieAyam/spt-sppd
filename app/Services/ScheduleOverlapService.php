<?php

namespace App\Services;

use App\Models\Spt;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class ScheduleOverlapService
{
    /**
     * Format tanggal ke Indonesia: 08 September 2026
     */
    public static function formatTanggalIndo(Carbon|string|null $tanggal): string
    {
        if (! $tanggal) {
            return '-';
        }

        $carbon = $tanggal instanceof Carbon
            ? $tanggal->copy()
            : Carbon::parse($tanggal);

        return $carbon->locale('id')->translatedFormat('d F Y');
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
}
