<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SPPD</title>
    @include('pdf.partials.styles')
    <style>
        @page { size: 215mm 330mm; margin: 5mm 8mm 5mm 8mm; }
        body { font-family: "Times New Roman", Times, serif; font-size: 11pt; margin: 0; padding: 0; color: #000; }

        /* ==== KOP (override partial shared dgn SPT hanya utk SPPD) ==== */
        .kop { padding-bottom: 4px; margin-bottom: 5mm; border-bottom: 2px double #000; }
        .kop-pemda { font-size: 12pt; line-height: 1.0; }
        .kop-dinas { font-size: 15pt; line-height: 1.1; margin-top: 1px; }
        .kop-alamat { font-size: 8pt; margin-top: 2px; line-height: 1.2; }
        .kop-logo img { width: 46px; }

        /* ==== DEPAN — GAP KOP → NOMOR → JUDUL → TABEL ==== */
        .nomor { text-align: right; margin: 10px 5mm 0 0; font-size: 10.5pt; line-height: 1.3; }
        .judul { text-align: center; margin: 10px 0 11px; }
        .judul-u { font-size: 14pt; font-weight: bold; letter-spacing: 1px; }
        .judul-s { font-size: 12.5pt; font-weight: bold; margin-top: 2px; }

        /* Tabel utama depan — NOMOR+LABEL menyatu, tidak ada garis vertikal nomor-label, tidak ada garis horizontal a/b/c */
        .tbl-depan { table-layout: fixed; border-collapse: collapse; margin: 0 5mm; width: calc(100% - 10mm); }
        .tbl-depan td { border: 1px solid #000; font-size: 11pt; line-height: 1.4; vertical-align: middle; }
        .tbl-depan .cell { padding: 5px 8px; }
        .tbl-depan .left-cell { width: 44%; }
        .tbl-depan .right-cell { width: 56%; }
        .inner-num-table { width: 100%; border-collapse: collapse; border: none; }
        .inner-num-table td { border: none; vertical-align: middle; padding: 0; }
        .num { width: 14%; text-align: center; font-size: 11pt; }
        .label-text { padding-left: 2px; line-height: 1.55; }
        .right-cell { line-height: 1.55; }
        .sub-line { line-height: 1.55; }
        .sub-line + .sub-line { margin-top: 1px; }
        .sub-marker { display: inline-block; width: 14px; }
        .sub-gap { display: inline-block; width: 9px; }
        .sub-lbl { padding-left: 22px; }

        /* Sub-tabel Pengikut: kolom Isi (label) + Keterangan */
        .tbl-pkg { width: 100%; border-collapse: collapse; table-layout: fixed; font-size: 9.5pt; }
        .tbl-pkg th, .tbl-pkg td { border: 1px solid #000; height: 20px; padding: 0; text-align: center; vertical-align: middle; }
        .tbl-pkg th { font-weight: normal; font-size: 9.5pt; }
        .tbl-lbl { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .tbl-lbl td { height: 20px; padding: 0; font-size: 11pt; }
        .tbl-lbl .k { font-weight: normal; }
        .tbl-lbl .n { padding-left: 22px; }

        /* TTD depan — PAKAI colon, blok kanan proporsional (+2mm) */
        .ttd-depan { width: 52%; margin-left: auto; margin-right: 5mm; margin-top: 19px; font-size: 11pt; line-height: 1.35; }
        .ttd-depan-l { font-weight: normal; width: 108px; }
        .ttd-depan-ct { text-align: center; margin-top: 10px; line-height: 1.4; }
        .ttd-depan-sp { height: 48px; }
        .ttd-depan-nm { font-weight: bold; text-decoration: underline; }
        .ttd-depan-sub { font-size: 10pt; }
        .ttd-depan-jab { font-weight: bold; }

        /* ==== BELAKANG — PAGE BREAK ==== */
        .page-break { page-break-before: always; }

        /* ==== BACK-PAGE ONLY — scoped, stabil untuk Dompdf ==== */
        .back-page .tbl-blk { width: 100%; border-collapse: collapse; table-layout: fixed; border: 1px solid #000; }
        .back-page .tbl-blk > tbody > tr > td { border: 1px solid #000; vertical-align: top; padding: 5px 7px; font-size: 10.5pt; line-height: 1.42; }
        .back-page .blk-l { width: 50%; }
        .back-page .blk-r { width: 50%; }

        .back-page .dotted-line { text-align: center; letter-spacing: 1px; font-size: 10pt; margin-top: 8px; white-space: nowrap; overflow: hidden; }
        .back-page .dotted-gap { height: 6mm; }
        .back-page .sig-jab { font-size: 8.5pt; line-height: 1.35; text-align: center; font-weight: normal; word-wrap: break-word; overflow-wrap: break-word; }
        .back-page .sig-name { font-weight: bold; text-decoration: underline; font-size: 10.5pt; line-height: 1.35; text-align: center; }
        .back-page .sig-nip { font-weight: normal; font-size: 9.5pt; line-height: 1.35; text-align: center; }
        
        .back-page .c-sep { font-weight: normal; }
        .back-page .kepala-dinas { text-align: center; font-weight: normal; font-size: 10.5pt; margin-top: 5px; margin-bottom: 5px; }
        .back-page .cat-j { font-weight: normal; font-size: 10.5pt; }
        .back-page .cat-t { font-weight: normal; font-size: 9.5pt; line-height: 1.5; text-align: justify; margin-left: 21px;  }
        .back-page .periksa-text { font-size: 10.5pt; line-height: 1.55; text-align: left; word-wrap: break-word; overflow-wrap: break-word; }
        .back-page .pada-tanggal { padding-left: 19px; }
        .back-page .pada-tanggal-3 { padding-left: 24px; }
        .back-page .c-sep { padding-left: 5mm; }
        
    </style>
</head>
<body>
@include('pdf.partials.kop')
@php
    $kdNama   = $penandatangan['nama']  ?? null;
    $kdJab    = $penandatangan['jabatan'] ?? null;
    $kdNip    = $penandatangan['nip']   ?? null;
    $kdPangkat = $penandatangan['pangkat'] ?? null;
    $kdGol    = $penandatangan['golongan'] ?? null;
    $sk       = config('pejabat-sementara.instansi.singkatan', 'DISKOMINFOSAN');
    $pt       = config('pejabat-sementara.pejabat_teknis', []);
    $namaPT   = $pt['nama'] ?? '';
    $nipPT    = $pt['nip'] ?? '';
    $pangkatPT = $pt['pangkat'] ?? '';
    $golPT    = $pt['golongan'] ?? '';
    $jabPT    = $pt['jabatan'] ?? 'KEPALA SUB BAGIAN UMUM DAN KEPEGAWAIAN SELAKU PEJABAT PELAKSANA TEKNIS KEGIATAN';
    $isRealPT = !empty($namaPT) && !empty($nipPT) && !str_starts_with($namaPT, '[');
    $formatNip = function($nip) {
        $n = preg_replace('/\D/', '', (string)$nip);
        if (strlen($n) === 18) {
            return substr($n,0,8).' '.substr($n,8,6).' '.substr($n,14,1).' '.substr($n,15,3);
        }
        return (string)$nip;
    };
    $pg = function($p, $g) {
        $out = [];
        if (!empty($p)) $out[] = $p;
        if (!empty($g)) $out[] = $g;
        return implode(' / ', $out);
    };
    $berangkat = 'Kantor ' . $sk . ', Paringin';
    $kotaAsal = 'Paringin';
    $tujuan = $spt->jenis_perjalanan === 'Dalam Daerah'
        ? trim(($spt->desa ? 'Desa ' . $spt->desa : '')
            . ($spt->kecamatan ? ', Kec. ' . $spt->kecamatan->nama : ''), ' ,')
        : ($spt?->kotaTujuan?->nama ?? '-');
    if ($tujuan === '') { $tujuan = '-'; }
    $pangkatPG = $pegawai?->pangkat ?? '';
    $golonganPG = $pegawai?->golongan ?? '';
@endphp
<div class="nomor">Nomor : {{ $sppd->nomor_sppd ?? '-' }}</div>
<div class="judul">
    <div class="judul-u">SURAT PERINTAH PERJALANAN DINAS</div>
    <div class="judul-s">(SPPD)</div>
</div>
<table class="tbl-depan">
    <tr>
        <td class="left-cell cell" style="height:13mm;">
            <table class="inner-num-table"><tr><td class="num">1</td><td class="label-text">Pengguna Anggaran/Kuasa Pengguna Anggaran</td></tr></table>
        </td>
        <td class="right-cell cell" style="height:13mm;">
            @if(!empty($kdNama) && !empty($kdJab))
                {{ $kdNama }} / {{ $kdJab }}
            @elseif(!empty($kdJab))
                {{ $kdJab }}
            @else
                Kepala Dinas Komunikasi, Informatika, Statistik dan Persandian Kabupaten Balangan
            @endif
        </td>
    </tr>
    <tr>
        <td class="left-cell cell" style="height:10mm;">
            <table class="inner-num-table"><tr><td class="num">2</td><td class="label-text">Nama dan NIP Pegawai yang Diperintahkan</td></tr></table>
        </td>
        <td class="right-cell cell" style="height:10mm;">{{ $pegawai?->nama ?? '-' }} / NIP. {{ $pegawai?->nip ?? '-' }}</td>
    </tr>
    <tr>
        <td class="left-cell cell" style="height:24mm;">
            <table class="inner-num-table"><tr><td class="num">3</td><td class="label-text"><div class="sub-line"><span class="sub-marker">a.</span><span class="sub-gap"></span>Pangkat dan Golongan</div><div class="sub-line"><span class="sub-marker">b.</span><span class="sub-gap"></span>Jabatan/Instansi</div><div class="sub-line"><span class="sub-marker">c.</span><span class="sub-gap"></span>Tingkat Biaya Perjalanan Dinas</div></td></tr></table>
        </td>
        <td class="right-cell cell" style="height:24mm;">
            <div class="sub-line"><span class="sub-marker">a.</span><span class="sub-gap"></span>{{ $pangkatPG }}@if($golonganPG) / {{ $golonganPG }}@endif</div><div class="sub-line"><span class="sub-marker">b.</span><span class="sub-gap"></span>{{ $pegawai?->jabatan ?? '-' }} / {{ $pegawai?->unit_kerja ?? config('pejabat-sementara.instansi.nama') }}</div><div class="sub-line"><span class="sub-marker">c.</span><span class="sub-gap"></span>-</div>
        </td>
    </tr>
    <tr>
        <td class="left-cell cell" style="height:11mm;">
            <table class="inner-num-table"><tr><td class="num">4</td><td class="label-text">Maksud Perjalanan Dinas</td></tr></table>
        </td>
        <td class="right-cell cell" style="height:11mm;">{{ $spt?->perihal ?? '-' }}</td>
    </tr>
    <tr>
        <td class="left-cell cell" style="height:7mm;">
            <table class="inner-num-table"><tr><td class="num">5</td><td class="label-text">Alat angkut yang dipergunakan</td></tr></table>
        </td>
        <td class="right-cell cell" style="height:7mm;">Kendaraan Dinas / Umum</td>
    </tr>
    <tr>
        <td class="left-cell cell" style="height:16mm;">
            <table class="inner-num-table"><tr><td class="num">6</td><td class="label-text"><div class="sub-line"><span class="sub-marker">a.</span><span class="sub-gap"></span>Tempat berangkat</div><div class="sub-line"><span class="sub-marker">b.</span><span class="sub-gap"></span>Tempat tujuan</div></td></tr></table>
        </td>
        <td class="right-cell cell" style="height:16mm;">
            <div class="sub-line"><span class="sub-marker">a.</span><span class="sub-gap"></span>{{ $berangkat }}</div><div class="sub-line"><span class="sub-marker">b.</span><span class="sub-gap"></span>{{ $tujuan }}</div>
        </td>
    </tr>
    <tr>
        <td class="left-cell cell" style="height:24mm;">
            <table class="inner-num-table"><tr><td class="num">7</td><td class="label-text"><div class="sub-line"><span class="sub-marker">a.</span><span class="sub-gap"></span>Lamanya Perjalanan Dinas</div><div class="sub-line"><span class="sub-marker">b.</span><span class="sub-gap"></span>Tanggal berangkat</div><div class="sub-line"><span class="sub-marker">c.</span><span class="sub-gap"></span>Tanggal harus kembali/tiba di tempat</div></td></tr></table>
        </td>
        <td class="right-cell cell" style="height:24mm;">
            <div class="sub-line"><span class="sub-marker">a.</span><span class="sub-gap"></span>@if($lamaHari){{ $lamaHari }} Hari ({{ $sppd->tanggal_berangkat?->translatedFormat('d F') ?? '-' }} s/d {{ $sppd->tanggal_kembali?->translatedFormat('d F Y') ?? '-' }})@else - @endif</div><div class="sub-line"><span class="sub-marker">b.</span><span class="sub-gap"></span>{{ $sppd->tanggal_berangkat?->translatedFormat('d F Y') ?? '-' }}</div><div class="sub-line"><span class="sub-marker">c.</span><span class="sub-gap"></span>{{ $sppd->tanggal_kembali?->translatedFormat('d F Y') ?? '-' }}</div>
        </td>
    </tr>
    <tr>
        <td class="left-cell cell" style="height:28mm;">
            <table class="inner-num-table"><tr><td class="num">8</td><td class="label-text"><div class="sub-line">Pengikut : Nama</div><div class="sub-line"><span class="sub-marker">1.</span><span class="sub-gap"></span></div><div class="sub-line"><span class="sub-marker">2.</span><span class="sub-gap"></span></div><div class="sub-line"><span class="sub-marker">3.</span><span class="sub-gap"></span></div></td></tr></table>
        </td>
        <td class="right-cell cell" style="height:28mm; padding:0;">
            <table class="tbl-pkg" style="width:100%; border-collapse:collapse; border:none;">
                <tr>
                    <th style="border:none; border-right:1px solid #000; border-bottom:1px solid #000;">Tanggal Lahir</th>
                    <th style="border:none; border-bottom:1px solid #000;">Keterangan</th>
                </tr>
                <tr><td style="border:none; border-right:1px solid #000; height:6mm;">&nbsp;</td><td style="border:none; height:6mm;">&nbsp;</td></tr>
                <tr><td style="border:none; border-right:1px solid #000; height:6mm;">&nbsp;</td><td style="border:none; height:6mm;">&nbsp;</td></tr>
                <tr><td style="border:none; border-right:1px solid #000; height:6mm;">&nbsp;</td><td style="border:none; height:6mm;">&nbsp;</td></tr>
                <tr><td style="border:none; border-right:1px solid #000; height:6mm;">&nbsp;</td><td style="border:none; height:6mm;">&nbsp;</td></tr>
                <tr><td style="border:none; border-right:1px solid #000; height:6mm;">&nbsp;</td><td style="border:none; height:2mm;">&nbsp;</td></tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="left-cell cell" style="height:24mm;">
            <table class="inner-num-table"><tr><td class="num">9</td><td class="label-text"><div class="sub-line">Pembebanan Anggaran</div><div class="sub-line"><span class="sub-marker">a.</span><span class="sub-gap"></span>SKPD</div><div class="sub-line"><span class="sub-marker">b.</span><span class="sub-gap"></span>Kode Rekening</div></td></tr></table>
        </td>
        <td class="right-cell cell" style="height:24mm;">
            <div class="sub-line">&nbsp;</div><div class="sub-line"><span class="sub-marker">a.</span><span class="sub-gap"></span>{{ config('pejabat-sementara.instansi.nama') }}</div><div class="sub-line"><span class="sub-marker">b.</span><span class="sub-gap"></span>{{ $pegawai?->kode_sppd ?? '-' }}</div>
        </td>
    </tr>
    <tr>
        <td class="left-cell cell" style="height:7mm;">
            <table class="inner-num-table"><tr><td class="num">10</td><td class="label-text">Keterangan lain-lain</td></tr></table>
        </td>
        <td class="right-cell cell" style="height:7mm;">-</td>
    </tr>
</table>
<div class="ttd-depan">
    <table style="width:100%; border-collapse:collapse;">
        <tr><td class="ttd-depan-l">Dikeluarkan di</td><td style="width:14px; text-align:center;">:</td><td>Paringin</td></tr>
        <tr><td class="ttd-depan-l">Pada Tanggal</td><td style="width:14px; text-align:center;">:</td><td>{{ $spt?->tanggal_spt?->translatedFormat('d F Y') ?? '-' }}</td></tr>
    </table>
    <div class="ttd-depan-ct">
        <div class="ttd-depan-jab">{{ $kdJab ?? 'KEPALA DINAS KOMUNIKASI, INFORMATIKA, STATISTIK DAN PERSANDIAN KABUPATEN BALANGAN' }}</div>
        <div class="ttd-depan-sp"></div>
        @if(!empty($kdNama))
            <div class="ttd-depan-nm">{{ $kdNama }}</div>
            @if(!empty($kdPangkat) || !empty($kdGol))<div class="ttd-depan-sub">{{ $pg($kdPangkat, $kdGol) }}</div>@endif
            @if(!empty($kdNip))<div>NIP. {{ $formatNip($kdNip) }}</div>@endif
        @else
            <div class="ttd-depan-nm" style="text-decoration:none;">........................................</div>
        @endif
    </div>
</div>
<div class="page-break"></div>
<div class="back-page">
<table class="tbl-blk">
    {{-- ROW I : kiri KOSONG, kanan Berangkat dari (tanpa I., tanpa TTD, wide) --}}
    <tr>
        <td class="blk-l" style="height:68mm; padding:0;">&nbsp;</td>
        <td class="blk-r" style="height:68mm;">
            <table class="f-tbl">
                <tr>
                    <td class="c-lab nowrap wide">Berangkat dari</td>
                    <td class="c-sep">:</td>
                    <td class="c-val">{{ $kotaAsal }}</td>
                </tr>
                <tr>
                    <td class="c-lab indent wide">Ke</td>
                    <td class="c-sep">:</td>
                    <td class="c-val">{{ $tujuan }}</td>
                </tr>
                <tr>
                    <td class="c-lab indent nowrap wide">Pada Tanggal</td>
                    <td class="c-sep">:</td>
                    <td class="c-val">{{ $sppd->tanggal_berangkat?->translatedFormat('d F Y') ?? '-' }}</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="blk-l" style="height:38mm;">
            <table class="f-tbl">
                <tr><td class="c-lab nowrap">II. Tiba di</td><td class="c-sep">:</td><td class="c-val">{{ $tujuan }}</td></tr>
                <tr><td class="c-lab pada-tanggal">Pada Tanggal</td><td class="c-sep">:</td><td class="c-val">{{ $sppd->tanggal_berangkat?->translatedFormat('d F Y') ?? '-' }}</td></tr>
            </table>
            <div style="height:17mm;"></div>
            <div class="dotted-line">.............................................</div>
        </td>
        <td class="blk-r" style="height:38mm;">
            <table class="f-tbl">
                <tr><td class="c-lab nowrap">Tiba di</td><td class="c-sep">:</td><td class="c-val">{{ $kotaAsal }}</td></tr>
                <tr><td class="c-lab nowrap">Pada Tanggal</td><td class="c-sep">:</td><td class="c-val">{{ $sppd->tanggal_kembali?->translatedFormat('d F Y') ?? '-' }}</td></tr>
            </table>
            <div style="height:17mm;"></div>
            <div class="dotted-line">.............................................</div>
        </td>
    </tr>
    <tr>
        <td class="blk-l" style="height:36mm;">
            <table class="f-tbl">
                <tr><td class="c-lab nowrap">III. Tiba di</td><td class="c-sep">:</td><td class="c-val">&nbsp;</td></tr>
                <tr><td class="c-lab pada-tanggal-3">Pada Tanggal</td><td class="c-sep">:</td><td class="c-val">&nbsp;</td></tr>
            </table>
            <div style="height:17mm;"></div>
            <div class="dotted-line">.............................................</div>
        </td>
        <td class="blk-r" style="height:36mm;">
            <table class="f-tbl">
                <tr><td class="c-lab nowrap">Tiba di</td><td class="c-sep">:</td><td class="c-val">&nbsp;</td></tr>
                <tr><td class="c-lab nowrap">Pada Tanggal</td><td class="c-sep">:</td><td class="c-val">&nbsp;</td></tr>
            </table>
            <div style="height:17mm;"></div>
            <div class="dotted-line">.............................................</div>
        </td>
    </tr>
    <tr>
        <td class="blk-l" style="height:64mm;">
            <table class="f-tbl">
                <tr><td class="c-lab nowrap">IV. Tiba di</td><td class="c-sep">:</td><td class="c-val">{{ $kotaAsal }}</td></tr>
                <tr><td class="c-lab pada-tanggal-3">Pada Tanggal</td><td class="c-sep">:</td><td class="c-val">{{ $sppd->tanggal_kembali?->translatedFormat('d F Y') ?? '-' }}</td></tr>
            </table>
            <div style="height:4mm;"></div>
            <div class="kepala-dinas">Kepala Dinas</div>
            <div style="height:30mm;"></div>
            <div style="text-align:center;">
                @if(!empty($kdNama))
                    <div class="sig-name">{{ $kdNama }}</div>
                    @if(!empty($kdNip))<div class="sig-nip">NIP. {{ $formatNip($kdNip) }}</div>@endif
                @else
                    <div class="sig-name">H. Syaifuddin Tailah, S.Pd, MM</div>
                    <div class="sig-nip">NIP. 19670403 199403 1 015</div>
                @endif
            </div>
        </td>
        <td class="blk-r" style="height:64mm; vertical-align:top;">
            <div class="periksa-text">Telah diperiksa, dengan keterangan bahwa perjalanan tersebut di atas dilakukan atas perintahnya dan semata-mata untuk kepentingan jabatan dalam waktu yang sesingkat-singkatnya.</div>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="padding:4px 7px; height:7mm; vertical-align:middle;">
            <div class="cat-j">V. CATATAN LAIN-LAIN</div>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="padding:5px 7px;">
            <div class="cat-j">VI. PERHATIAN</div>
            <div class="cat-t">Pengguna Anggaran/Kuasa Pengguna Anggaran yang menerbitkan SPD, Pejabat/Pegawai/Pihak Lain yang melakukan Perjalanan Dinas, para pejabat yang mengesahkan Tanggal Berangkat/Tiba serta Bendahara Pengeluaran bertanggung jawab berdasarkan Peraturan-Peraturan Keuangan Daerah apabila Negara menderita rugi akibat Kesalahan, Kelalaian dan Kealpaannya.</div>
        </td>
    </tr>
</table>
</div>
</body>
</html>
