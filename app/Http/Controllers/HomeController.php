<?php

namespace App\Http\Controllers;

use App\Models\Spt;
use App\Models\Sppd;

class HomeController extends Controller
{
    public function index()
    {
        $totalSpt = Spt::count();

        $totalSppd = Sppd::count();

        $dalamDaerah = Spt::where('jenis_perjalanan', 'Dalam Daerah')
            ->count();

        $luarDaerah = Spt::where('jenis_perjalanan', 'Luar Daerah')
            ->count();

        $data = Sppd::with([
            'spt.kecamatan',
            'spt.kotaTujuan',
            'pegawai',
        ])
            ->latest()
            ->take(10)
            ->get();

        return view('home', [
            'totalSpt' => $totalSpt,
            'totalSppd' => $totalSppd,
            'dalamDaerah' => $dalamDaerah,
            'luarDaerah' => $luarDaerah,
            'data' => $data,
        ]);
    }
}