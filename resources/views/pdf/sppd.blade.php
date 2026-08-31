<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SPPD</title>
    @include('pdf.partials.styles')
    <style>
        @page { size: 215mm 330mm; margin: 8mm; }
        body { font-family: "Times New Roman", Times, serif; font-size: 11pt; margin: 0; padding: 0; color: #000; }

        /* ==== KOP (override partial shared dgn SPT hanya utk SPPD) ==== */
        .kop { padding-bottom: 2px; margin-bottom: 3px; border-bottom: 2px double #000; }
        .kop-pemda { font-size: 12pt; line-height: 1.0; }
        .kop-dinas { font-size: 15pt; line-height: 1.1; margin-top: 1px; }
        .kop-alamat { font-size: 8pt; margin-top: 2px; line-height: 1.2; }
        .kop-logo img { width: 46px; }

        /* ==== DEPAN ==== */
        .nomor { text-align: right; margin: 2px 0 4px; }
        .judul { text-align: center; margin: 0 0 5px; }
        .judul-u { font-size: 14pt; font-weight: bold; letter-spacing: 1px; }
        .judul-s { font-size: 12.5pt; font-weight: bold; margin-top: 2px; }

        /* Tabel utama depan — TANPA colon pemisah, nomor polos tanpa titik */
        .tbl-depan { table-layout: fixed; border-collapse: collapse; margin: 0 5mm; width: calc(100% - 10mm); }
        .tbl-depan td { border: 1px solid #000; vertical-align: top; font-size: 11pt; line-height: 1.4; }
        .tbl-depan .cell { padding: 5px 8px; }
        .tbl-depan .td-no { width: 6%; text-align: center; border-right: none; }
        .tbl-depan .td-lbl { width: 38%; border-left: none; }
        .td-isi { width: 56%; }
        .sub-lbl { padding-left: 22px; }

        /* Sub-tabel Pengikut: kolom Isi (label) + Keterangan */
        .tbl-pkg { width: 100%; border-collapse: collapse; table-layout: fixed; font-size: 9.5pt; }
        .tbl-pkg th, .tbl-pkg td { border: 1px solid #000; height: 20px; padding: 0; text-align: center; vertical-align: middle; }
        .tbl-pkg th { font-weight: bold; font-size: 9.5pt; }
        .tbl-lbl { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .tbl-lbl td { height: 20px; padding: 0; font-size: 11pt; }
        .tbl-lbl .k { font-weight: bold; }
        .tbl-lbl .n { padding-left: 22px; }

        /* TTD depan — PAKAI colon (teks bebas di luar grid) */
        .ttd-depan { width: 60%; margin-left: auto; margin-top: 4px; font-size: 11pt; }
        .ttd-depan-l { font-weight: bold; }
        .ttd-depan-ct { text-align: center; margin-top: 4px; line-height: 1.3; }
        .ttd-depan-sp { height: 30px; }
        .ttd-depan-nm { font-weight: bold; text-decoration: underline; }
        .ttd-depan-sub { font-size: 10pt; }

        /* ==== BELAKANG ==== */
        .page-break { page-break-before: always; }

        .tbl-blk { width: 100%; border-collapse: collapse; table-layout: fixed; border: 1px solid #000; }
        .tbl-blk td { border: 1px solid #000; vertical-align: top; padding: 5px 8px; font-size: 10.5pt; line-height: 1.4; }
        .blk-l { width: 50%; }
        .blk-r { width: 50%; }

        .et-j { font-weight: bold; font-size: 11pt; margin-bottom: 4px; }
        .et-dp { font-weight: bold; }
        .et-f { font-size: 10.5pt; line-height: 1.4; margin-bottom: 3px; }

        .ttd-in { text-align: center; margin-top: 10px; font-size: 10pt; line-height: 1.4; }
        .ttd-in-sp { height: 44px; }
        .ttd-in-nm { font-weight: bold; text-decoration: underline; }
        .ttd-in-sub { font-size: 9.5pt; }

        .cat-j { font-weight: bold; font-size: 10.5pt; margin-bottom: 4px; }
        .cat-t { font-size: 9.5pt; line-height: 1.5; text-align: justify; }
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
    $tujuan = $spt->jenis_perjalanan === 'Dalam Daerah'
        ? trim(($spt->desa ? 'Desa ' . $spt->desa : '')
            . ($spt->kecamatan ? ', Kec. ' . $spt->kecamatan->nama : ''), ' ,')
        : ($spt?->kotaTujuan?->nama ?? '-');
    if ($tujuan === '') { $tujuan = '-'; }

    $pangkatPG = $pegawai?->pangkat ?? '';
    $golonganPG = $pegawai?->golongan ?? '';
@endphp

{{-- ============================================ --}}
{{-- HALAMAN 1 — DEPAN (TANPA colon di tabel)     --}}
{{-- ============================================ --}}

<div class="nomor">Nomor : {{ $sppd->nomor_sppd ?? '-' }}</div>

<div class="judul">
    <div class="judul-u">SURAT PERINTAH PERJALANAN DINAS</div>
    <div class="judul-s">(SPPD)</div>
</div>

<table class="tbl-depan">

    {{-- 1 --}}
    <tr>
        <td class="td-no cell" style="height:13mm;">1</td>
        <td class="td-lbl cell" style="height:13mm;">Pengguna Anggaran/Kuasa Pengguna Anggaran</td>
        <td class="td-isi cell" style="height:13mm;">
            @if(!empty($kdNama) && !empty($kdJab))
                {{ $kdNama }} / {{ $kdJab }}
            @elseif(!empty($kdJab))
                {{ $kdJab }}
            @else
                Kepala Dinas Komunikasi, Informatika, Statistik dan Persandian Kabupaten Balangan
            @endif
        </td>
    </tr>

    {{-- 2 --}}
    <tr>
        <td class="td-no cell" style="height:10mm;">2</td>
        <td class="td-lbl cell" style="height:10mm;">Nama dan NIP Pegawai yang Diperintahkan</td>
        <td class="td-isi cell" style="height:10mm;">{{ $pegawai?->nama ?? '-' }} / NIP. {{ $pegawai?->nip ?? '-' }}</td>
    </tr>

    {{-- 3 : SATU grup tanpa border internal a/b/c --}}
    <tr>
        <td class="td-no cell" style="height:24mm; vertical-align:middle;">3</td>
        <td class="td-lbl cell sub-lbl" style="vertical-align:top;">
            a. Pangkat dan Golongan<br>
            b. Jabatan/Instansi<br>
            c. Tingkat Biaya Perjalanan Dinas
        </td>
        <td class="td-isi cell" style="vertical-align:top;">
            a. {{ $pangkatPG }}@if($golonganPG) / {{ $golonganPG }}@endif<br>
            b. {{ $pegawai?->jabatan ?? '-' }} / {{ $pegawai?->unit_kerja ?? config('pejabat-sementara.instansi.nama') }}<br>
            c. -
        </td>
    </tr>

    {{-- 4 --}}
    <tr>
        <td class="td-no cell" style="height:11mm;">4</td>
        <td class="td-lbl cell" style="height:11mm;">Maksud Perjalanan Dinas</td>
        <td class="td-isi cell" style="height:11mm;">{{ $spt?->perihal ?? '-' }}</td>
    </tr>

    {{-- 5 --}}
    <tr>
        <td class="td-no cell" style="height:7mm;">5</td>
        <td class="td-lbl cell" style="height:7mm;">Alat angkut yang dipergunakan</td>
        <td class="td-isi cell" style="height:7mm;">Kendaraan Dinas / Umum</td>
    </tr>

    {{-- 6 : SATU grup tanpa border internal a/b --}}
    <tr>
        <td class="td-no cell" style="height:16mm; vertical-align:middle;">6</td>
        <td class="td-lbl cell sub-lbl" style="vertical-align:top;">
            a. Tempat berangkat<br>
            b. Tempat tujuan
        </td>
        <td class="td-isi cell" style="vertical-align:top;">
            a. {{ $berangkat }}<br>
            b. {{ $tujuan }}
        </td>
    </tr>

    {{-- 7 : SATU grup tanpa border internal a/b/c --}}
    <tr>
        <td class="td-no cell" style="height:24mm; vertical-align:middle;">7</td>
        <td class="td-lbl cell sub-lbl" style="vertical-align:top;">
            a. Lamanya Perjalanan Dinas<br>
            b. Tanggal berangkat<br>
            c. Tanggal harus kembali/tiba di tempat
        </td>
        <td class="td-isi cell" style="vertical-align:top;">
            a. @if($lamaHari){{ $lamaHari }} Hari ({{ $sppd->tanggal_berangkat?->translatedFormat('d F') ?? '-' }} s/d {{ $sppd->tanggal_kembali?->translatedFormat('d F Y') ?? '-' }})@else - @endif<br>
            b. {{ $sppd->tanggal_berangkat?->translatedFormat('d F Y') ?? '-' }}<br>
            c. {{ $sppd->tanggal_kembali?->translatedFormat('d F Y') ?? '-' }}
        </td>
    </tr>

    {{-- 8 : PENGIKUT — sub-tabel TANPA kolom No, angka di kolom label --}}
    <tr>
        <td class="td-no cell" style="height:28mm; vertical-align:top;">8</td>
        <td class="td-lbl cell" style="height:28mm; vertical-align:top; padding:0;">
            <table class="tbl-lbl" style="width:100%; border-collapse:collapse;">
                <tr><td class="k" style="border:none;">Pengikut : Nama</td></tr>
                <tr><td class="n" style="border:none;">1.</td></tr>
                <tr><td class="n" style="border:none;">2.</td></tr>
                <tr><td class="n" style="border:none;">3.</td></tr>
            </table>
        </td>
        <td class="td-isi cell" style="height:28mm; padding:0; vertical-align:top;">
            <table class="tbl-pkg" style="width:100%; border-collapse:collapse; border:none;">
                <tr>
                    <th style="border:none; border-right:1px solid #000; border-bottom:1px solid #000;">Tanggal Lahir</th>
                    <th style="border:none; border-bottom:1px solid #000;">Keterangan</th>
                </tr>
                <tr>
                    <td style="border:none; border-right:1px solid #000; height:6mm;">&nbsp;</td>
                    <td style="border:none; height:6mm;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="border:none; border-right:1px solid #000; height:6mm;">&nbsp;</td>
                    <td style="border:none; height:6mm;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="border:none; border-right:1px solid #000; height:6mm;">&nbsp;</td>
                    <td style="border:none; height:6mm;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="border:none; border-right:1px solid #000; height:6mm;">&nbsp;</td>
                    <td style="border:none; height:6mm;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="border:none; border-right:1px solid #000; height:6mm;">&nbsp;</td>
                    <td style="border:none; height:2mm;">&nbsp;</td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- 9 : SATU grup — "Pembebanan Anggaran" lalu a/b, tanpa border internal --}}
    <tr>
        <td class="td-no cell" style="height:24mm; vertical-align:middle;">9</td>
        <td class="td-lbl cell" style="vertical-align:top;">
            Pembebanan Anggaran<br>
            a. SKPD<br>
            b. Kode Rekening
        </td>
        <td class="td-isi cell" style="vertical-align:top;">
            &nbsp;<br>
            a. {{ config('pejabat-sementara.instansi.nama') }}<br>
            b. {{ $pegawai?->kode_sppd ?? '-' }}
        </td>
    </tr>

    {{-- 10 --}}
    <tr>
        <td class="td-no cell" style="height:7mm;">10</td>
        <td class="td-lbl cell" style="height:7mm;">Keterangan lain-lain</td>
        <td class="td-isi cell" style="height:7mm;">-</td>
    </tr>

</table>

{{-- TTD DEPAN — PAKAI colon, urutan Nama → Pangkat/Golongan → NIP --}}
<div class="ttd-depan">
    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td class="ttd-depan-l">Dikeluarkan di</td>
            <td style="width:14px; text-align:center;">:</td>
            <td>Paringin</td>
        </tr>
        <tr>
            <td class="ttd-depan-l">Pada Tanggal</td>
            <td style="width:14px; text-align:center;">:</td>
            <td>{{ $spt?->tanggal_spt?->translatedFormat('d F Y') ?? '-' }}</td>
        </tr>
    </table>
    <div class="ttd-depan-ct">
        {{ $kdJab ?? 'KEPALA DINAS KOMUNIKASI, INFORMATIKA, STATISTIK DAN PERSANDIAN KABUPATEN BALANGAN' }}
        <div class="ttd-depan-sp"></div>
        <div class="ttd-depan-nm">{{ $kdNama ?? '( ' . $sk . ' )' }}</div>
        @if(!empty($kdPangkat) || !empty($kdGol))
            <div class="ttd-depan-sub">{{ $pg($kdPangkat, $kdGol) }}</div>
        @endif
        @if(!empty($kdNip))<div>NIP. {{ $formatNip($kdNip) }}</div>@endif
    </div>
</div>


{{-- ============================================ --}}
{{-- HALAMAN 2 — BELAKANG (PAKAI colon)          --}}
{{-- SATU FORMULIR/TABEL BESAR MENYAMBUNG        --}}
{{-- ============================================ --}}

<div class="page-break">

<table class="tbl-blk">

    {{-- SEKSI I : kiri KOSONG, kanan isi + TTD pejabat teknis --}}
    <tr>
        <td class="blk-l" style="height:72mm;">&nbsp;</td>
        <td class="blk-r" style="height:72mm;">
            <div class="et-j">I.</div>
            <div class="et-f">
                <span class="et-dp">Berangkat dari</span> (Tempat Kedudukan) :
                <br>{{ $berangkat }}
            </div>
            <div class="et-f">
                <span class="et-dp">Ke</span> :
                <br>{{ $tujuan }}
            </div>
            <div class="et-f">
                <span class="et-dp">Pada Tanggal</span> :
                <br>{{ $sppd->tanggal_berangkat?->translatedFormat('d F Y') ?? '-' }}
            </div>
            <div class="ttd-in">
                <strong>{{ $jabPT }}</strong>
                <div class="ttd-in-sp"></div>
                @if($isRealPT)
                    <div class="ttd-in-nm">{{ $namaPT }}</div>
                    @if(!empty($pangkatPT) || !empty($golPT))
                        <div class="ttd-in-sub">{{ $pg($pangkatPT, $golPT) }}</div>
                    @endif
                    <div class="ttd-in-sub">NIP. {{ $formatNip($nipPT) }}</div>
                @else
                    <div class="ttd-in-nm">{{ $jabPT }}</div>
                @endif
            </div>
        </td>
    </tr>

    {{-- SEKSI II : kiri-kanan menyatu, semua KOSONG (Kepala kosong) --}}
    <tr>
        <td class="blk-l" style="height:34mm;">
            <div class="et-j">II.</div>
            <div class="et-f"><span class="et-dp">Tiba di</span> :</div>
            <div class="et-f"><span class="et-dp">Pada Tanggal</span> :</div>
            <div class="et-f"><span class="et-dp">Kepala</span> :</div>
        </td>
        <td class="blk-r" style="height:34mm;">
            <div class="et-f"><span class="et-dp">Tiba di</span> :</div>
            <div class="et-f"><span class="et-dp">Pada Tanggal</span> :</div>
            <div class="et-f"><span class="et-dp">Kepala</span> :</div>
        </td>
    </tr>

    {{-- SEKSI III : kiri-kanan menyatu, semua value kosong --}}
    <tr>
        <td class="blk-l" style="height:34mm;">
            <div class="et-j">III.</div>
            <div class="et-f"><span class="et-dp">Tiba di</span> :</div>
            <div class="et-f"><span class="et-dp">Pada Tanggal</span> :</div>
            <div class="et-f"><span class="et-dp">Kepala</span> :</div>
        </td>
        <td class="blk-r" style="height:34mm;">
            <div class="et-f"><span class="et-dp">Tiba di</span> :</div>
            <div class="et-f"><span class="et-dp">Pada Tanggal</span> :</div>
            <div class="et-f"><span class="et-dp">Kepala</span> :</div>
        </td>
    </tr>

    {{-- SEKSI IV : kiri isi + TTD Kepala Dinas, kanan "Telah diperiksa" --}}
    <tr>
        <td class="blk-l" style="height:78mm;">
            <div class="et-j">IV.</div>
            <div class="et-f">
                <span class="et-dp">Tiba di</span> :
                <br>{{ $berangkat }}
            </div>
            <div class="et-f">
                <span class="et-dp">Pada Tanggal</span> :
                <br>{{ $sppd->tanggal_kembali?->translatedFormat('d F Y') ?? '-' }}
            </div>
            <div class="et-f"><span class="et-dp">Kepala</span> :</div>

            <div class="ttd-in">
                <div class="ttd-in-sp"></div>
                @if(!empty($kdNama))
                    <div class="ttd-in-nm">{{ $kdNama }}</div>
                    @if(!empty($kdPangkat) || !empty($kdGol))
                        <div class="ttd-in-sub">{{ $pg($kdPangkat, $kdGol) }}</div>
                    @endif
                    @if(!empty($kdNip))<div class="ttd-in-sub">NIP. {{ $formatNip($kdNip) }}</div>@endif
                @else
                    <div class="ttd-in-nm">( {{ $sk }} )</div>
                @endif
            </div>
        </td>
        <td class="blk-r" style="height:78mm;">
            <div class="et-f">
                Telah diperiksa, dengan keterangan bahwa perjalanan tersebut di atas dilakukan atas perintahnya dan semata-mata untuk kepentingan jabatan dalam waktu yang sesingkat-singkatnya.
            </div>
        </td>
    </tr>

    {{-- SEKSI V : full width, heading + kotak kosong --}}
    <tr>
        <td colspan="2" style="height:34mm;">
            <div class="cat-j">V. CATATAN LAIN-LAIN</div>
            <div style="height:20mm;">&nbsp;</div>
        </td>
    </tr>

    {{-- SEKSI VI : full width, heading + paragraf resmi --}}
    <tr>
        <td colspan="2" style="height:30mm;">
            <div class="cat-j">VI. PERHATIAN</div>
            <div class="cat-t">
                Pengguna Anggaran/Kuasa Pengguna Anggaran yang menerbitkan SPD, Pejabat/Pegawai/Pihak Lain yang melakukan Perjalanan Dinas, para pejabat yang mengesahkan Tanggal Berangkat/Tiba serta Bendahara Pengeluaran bertanggung jawab berdasarkan Peraturan-Peraturan Keuangan Daerah apabila Negara menderita rugi akibat Kesalahan, Kelalaian dan Kealpaannya.
            </div>
        </td>
    </tr>

</table>

</div>

</body>
</html>
