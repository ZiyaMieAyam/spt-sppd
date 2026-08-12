<?php

namespace App\Http\Controllers;

use App\Models\Spt;
use Barryvdh\DomPDF\Facade\Pdf;

class SptPdfController extends Controller
{
    public function generate(Spt $spt)
    {
        $spt->load([
            'pegawais',
            'kecamatan',
            'kotaTujuan',
        ]);

        $logoPath = public_path('images/logo-balangan.png');

        $pdf = Pdf::loadView('pdf.spt', [
            'spt' => $spt,
            'logoPath' => $logoPath,
        ]);

        $pdf->setPaper('A4', 'portrait');

        $dompdf = $pdf->getDomPDF();

        $dompdf->set_option('chroot', public_path());

        return $pdf->stream(
            'SPT-' . $spt->id . '.pdf'
        );
    }
}