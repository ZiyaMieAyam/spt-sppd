<?php

namespace App\Http\Controllers;

use App\Models\Sppd;
use App\Models\Spt;

class BerandaController extends Controller
{
    public function index()
    {
        return view('beranda', [
            'totalSpt' => Spt::count(),
            'totalSppd' => Sppd::count(),
            'dalamDaerah' => Spt::where(
                'jenis_perjalanan',
                'Dalam Daerah'
            )->count(),
            'luarDaerah' => Spt::where(
                'jenis_perjalanan',
                'Luar Daerah'
            )->count(),
            'terbaru' => Spt::with([
                'kecamatan',
                'kotaTujuan',
                'pegawais',
            ])
                ->orderByDesc('tanggal_spt')
                ->orderByDesc('id')
                ->limit(5)
                ->get(),
        ]);
    }
}
