<?php

namespace App\Http\Controllers;

use App\Models\Sppd;

class PerjalananDinasController extends Controller
{
    public function dalamDaerah()
    {
        $data = Sppd::with([
            'spt.kecamatan',
            'pegawai',
        ])
            ->whereHas('spt', function ($query) {
                $query->where(
                    'jenis_perjalanan',
                    'Dalam Daerah'
                );
            })
            ->orderBy('spt_id', 'asc')
            ->orderBy('id', 'asc')
            ->paginate(15);

        return view('dalam-daerah', [
            'data' => $data,
        ]);
    }

    public function luarDaerah()
    {
        $data = Sppd::with([
            'spt.kotaTujuan',
            'pegawai',
        ])
            ->whereHas('spt', function ($query) {
                $query->where(
                    'jenis_perjalanan',
                    'Luar Daerah'
                );
            })
            ->orderBy('spt_id', 'asc')
            ->orderBy('id', 'asc')
            ->paginate(15);

        return view('luar-daerah', [
            'data' => $data,
        ]);
    }
}
