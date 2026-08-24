<?php

namespace App\Http\Controllers;

use App\Models\Sppd;
use App\Services\PenandatanganService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SppdPdfController extends Controller
{
    public function show(Request $request, Sppd $sppd)
    {
        $kunci = $request->query('penandatangan');

        $definisi = is_string($kunci) && $kunci !== ''
            ? PenandatanganService::cari($kunci)
            : null;

        if ($definisi === null) {
            return redirect()
                ->route('spts.pdf.pilih', [
                    'spt' => $sppd->spt_id,
                    'sppd' => $sppd->id,
                ])
                ->with('error', 'Silakan pilih penandatangan terlebih dahulu.');
        }

        $sppd->load([
            'pegawai',
            'spt.kecamatan',
            'spt.kotaTujuan',
        ]);

        if (!PenandatanganService::valid($kunci, [$sppd->pegawai])) {
            return redirect()
                ->route('spts.pdf.pilih', [
                    'spt' => $sppd->spt_id,
                    'sppd' => $sppd->id,
                ])
                ->with('error', 'Penandatangan tersebut tidak diizinkan untuk jabatan pegawai yang ditugaskan.');
        }

        $lamaHari = null;

        if ($sppd->tanggal_berangkat && $sppd->tanggal_kembali) {
            $lamaHari = (int) $sppd->tanggal_berangkat->diffInDays($sppd->tanggal_kembali) + 1;
        }

        $pathKop = PenandatanganService::pathKop((string) $definisi['kop']);

        $pdf = Pdf::loadView('pdf.sppd', [
            'sppd' => $sppd,
            'spt' => $sppd->spt,
            'pegawai' => $sppd->pegawai,
            'lamaHari' => $lamaHari,
            'penandatangan' => $definisi,
            'pathKop' => $pathKop,
            'adaGambarKop' => $pathKop !== '' && is_file($pathKop),
        ]);

        $pdf->setPaper('A4', 'portrait');

        $dompdf = $pdf->getDomPDF();

        $dompdf->set_option('chroot', public_path());

        return $pdf->stream('SPPD-' . $sppd->id . '.pdf');
    }
}
