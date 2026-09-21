<?php

namespace App\Http\Controllers;

use App\Models\Sppd;
use App\Services\PenandatanganService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Config;

class SppdPdfController extends Controller
{
    public function show(Sppd $sppd)
    {
        $sppd->load([
            'pegawai',
            'spt.kecamatan',
            'spt.kotaTujuan',
        ]);

        $penandatangan = PenandatanganService::cari('kepala_dinas') ?? Config::get('pejabat-sementara.kepala_dinas', [
            'nama' => null,
            'nip' => null,
            'jabatan' => 'KEPALA DINAS KOMUNIKASI, INFORMATIKA, STATISTIK DAN PERSANDIAN KABUPATEN BALANGAN',
        ]);
        // Kop SPPD harus Sekretariat Daerah sesuai referensi gambar (tanpa ubah back-page)
        $penandatangan['kop'] = 'sekda';

        $lamaHari = null;

        if ($sppd->tanggal_berangkat && $sppd->tanggal_kembali) {
            $lamaHari = (int) $sppd->tanggal_berangkat->diffInDays($sppd->tanggal_kembali) + 1;
        }

        $pdf = Pdf::loadView('pdf.sppd', [
            'sppd' => $sppd,
            'spt' => $sppd->spt,
            'pegawai' => $sppd->pegawai,
            'lamaHari' => $lamaHari,
            'penandatangan' => $penandatangan,
            'pathKop' => '',
            'adaGambarKop' => false,
        ]);

        $pdf->setPaper('A4', 'portrait');

        $dompdf = $pdf->getDomPDF();

        $dompdf->set_option('chroot', public_path());

        return $pdf->stream('SPPD-'.$sppd->id.'.pdf');
    }
}
