<?php

namespace App\Http\Controllers;

use App\Models\Sppd;
use App\Models\Spt;
use App\Services\PenandatanganService;
use App\Support\PegawaiSorter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class SptPdfController extends Controller
{
    public function pilih(Request $request, Spt $spt)
    {
        $pegawais = $this->pegawaiTerurut($spt);

        $sppd = null;

        if ($request->filled('sppd')) {
            $sppd = Sppd::where('spt_id', $spt->id)
                ->find($request->integer('sppd'));
        }

        return view('pdf.pilih-penandatangan', [
            'spt' => $spt,
            'sppd' => $sppd,
            'pegawais' => $pegawais,
            'semuaPenandatangan' => PenandatanganService::semua(),
            'penandatanganDiizinkan' => PenandatanganService::yangDiizinkan($pegawais),
        ]);
    }

    public function generate(Request $request, Spt $spt)
    {
        [$definisi, $kunci] = $this->resolusiPenandatangan($request, $spt);

        if ($definisi === null) {
            return $this->redirectBelumPilih($spt);
        }

        $pegawais = $this->pegawaiTerurut($spt);

        if (! PenandatanganService::valid($kunci, $pegawais)) {
            return $this->redirectBelumPilih($spt)
                ->with('error', 'Penandatangan tersebut tidak diizinkan untuk jabatan pegawai yang ditugaskan.');
        }

        $spt->load([
            'kecamatan',
            'kotaTujuan',
        ]);

        $pdf = Pdf::loadView('pdf.spt', [
            'spt' => $spt,
            'pegawais' => $pegawais,
            'penandatangan' => $definisi,
            ...$this->variabelKop($definisi),
        ]);

        return $this->stream($pdf, 'SPT-'.$spt->id.'.pdf');
    }

    private function pegawaiTerurut(Spt $spt)
    {
        return PegawaiSorter::urutkan(
            $spt->pegawais()->get()
        );
    }

    private function resolusiPenandatangan(Request $request, Spt|Sppd $dokumen): array
    {
        $kunci = $request->query('penandatangan');

        $definisi = is_string($kunci) && $kunci !== ''
            ? PenandatanganService::cari($kunci)
            : null;

        if ($definisi !== null) {
            $definisi = $this->mergePejabatSementara($definisi, $kunci);
        }

        return [$definisi, is_string($kunci) ? $kunci : ''];
    }

    private function redirectBelumPilih(Spt $spt)
    {
        return redirect()->route('spts.pdf.pilih', $spt)
            ->with('error', 'Silakan pilih penandatangan terlebih dahulu.');
    }

    private function variabelKop(array $definisi): array
    {
        $pathKop = PenandatanganService::pathKop((string) $definisi['kop']);

        return [
            'pathKop' => $pathKop,
            'adaGambarKop' => $pathKop !== '' && is_file($pathKop),
        ];
    }

    private function stream($pdf, string $namaFile)
    {
        $pdf->setPaper('A4', 'portrait');

        $dompdf = $pdf->getDomPDF();

        $dompdf->set_option('chroot', public_path());

        return $pdf->stream($namaFile);
    }

    private function mergePejabatSementara(array $definisi, string $kunci): array
    {
        $mapping = [
            'bupati' => 'bupati',
            'wakil-bupati' => 'wakil_bupati',
            'sekda' => 'sekda',
            'kepala-diskominfo' => 'kepala_diskominfo',
        ];

        $sementaraKey = $mapping[$kunci] ?? null;

        if ($sementaraKey === null) {
            return $definisi;
        }

        $sementara = Config::get("pejabat-sementara.{$sementaraKey}", []);

        foreach (['nama', 'nip', 'pangkat', 'golongan', 'jabatan'] as $field) {
            if (empty($definisi[$field]) && ! empty($sementara[$field])) {
                $definisi[$field] = $sementara[$field];
            }
        }

        return $definisi;
    }
}
