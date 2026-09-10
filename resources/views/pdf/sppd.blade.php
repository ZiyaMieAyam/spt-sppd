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
        .label-text { padding-left: 2px; line-height: 0.55; }
        .right-cell { line-height: 1.40; }
        .sub-line { line-height: 1.40; }
        .sub-line + .sub-line { margin-top: 5px; }
        .sub-marker { line-height: 1.40; font-weight: normal; }
        .sub-gap { display: inline-block; width: 10px; }

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

        .back-page .dotted-line { text-align: center; letter-spacing: 1px; font-size: 10pt; margin-top: 18mm; white-space: nowrap; overflow: hidden; }
        .back-page .dotted-gap { height: 6mm; }
        .back-page .sig-jab { font-size: 8.5pt; line-height: 1.35; text-align: center; font-weight: normal; word-wrap: break-word; overflow-wrap: break-word; }
        .back-page .sig-name { font-weight: bold; text-decoration: underline; font-size: 10.5pt; line-height: 1.35; text-align: center; }
        .back-page .sig-nip { font-weight: normal; font-size: 9.5pt; line-height: 1.35; text-align: center; }

        .back-page .c-sep { font-weight: normal; }
        .back-page .kepala-dinas { text-align: center; font-weight: normal; font-size: 10.5pt; margin-top: 5px; margin-bottom: 5px; }
        .back-page .cat-j { font-weight: normal; font-size: 10.5pt; }
        .back-page .cat-t { font-weight: normal; font-size: 9.5pt; line-height: 1.5; text-align: justify; margin-left: 21px; }
        .back-page .periksa-text { font-size: 10.5pt; line-height: 1.55; text-align: left; word-wrap: break-word; overflow-wrap: break-word; }
        .back-page .pada-tanggal { padding-left: 19px; }
        .back-page .pada-tanggal-3 { padding-left: 24px; }
        .back-page .c-sep { padding-left: 5mm; }

        /* ==== TEXT FITTING ITEM 5-10 — hanya font-size/line-height menyesuaikan, tinggi cell tetap ==== */
        .fit-box { overflow: hidden; width: 100%; display: block; box-sizing: border-box; }
        .fit-box.fit-5, .fit-box.fit-10 { max-height: 7mm; }
        .fit-box.fit-6 { max-height: 16mm; }
        .fit-box.fit-7, .fit-box.fit-9 { max-height: 24mm; }
        .fit-box.fit-8 { max-height: 28mm; }
        /* Dompdf: pastikan td tetap fixed height, tidak auto-expand */
        .tbl-depan td.fit-cell { overflow: hidden; }
    </style>
</head>
<body>
    @include('pdf.partials.kop')

    @php
        $kdNama    = $penandatangan['nama'] ?? null;
        $kdJab     = $penandatangan['jabatan'] ?? null;
        $kdNip     = $penandatangan['nip'] ?? null;
        $kdPangkat = $penandatangan['pangkat'] ?? null;
        $kdGol     = $penandatangan['golongan'] ?? null;
        $sk        = config('pejabat-sementara.instansi.singkatan', 'DISKOMINFOSAN');
        $pt        = config('pejabat-sementara.pejabat_teknis', []);
        $namaPT    = $pt['nama'] ?? '';
        $nipPT     = $pt['nip'] ?? '';
        $pangkatPT = $pt['pangkat'] ?? '';
        $golPT     = $pt['golongan'] ?? '';
        $jabPT     = $pt['jabatan'] ?? 'KEPALA SUB BAGIAN UMUM DAN KEPEGAWAIAN SELAKU PEJABAT PELAKSANA TEKNIS KEGIATAN';
        $isRealPT  = !empty($namaPT) && !empty($nipPT) && !str_starts_with($namaPT, '[');

        $formatNip = function ($nip) {
            $n = preg_replace('/\D/', '', (string) $nip);
            if (strlen($n) === 18) {
                return substr($n, 0, 8) . ' ' . substr($n, 8, 6) . ' ' . substr($n, 14, 1) . ' ' . substr($n, 15, 3);
            }
            return (string) $nip;
        };

        $pg = function ($p, $g) {
            $out = [];
            if (!empty($p)) $out[] = $p;
            if (!empty($g)) $out[] = $g;
            return implode(' / ', $out);
        };

        $berangkat = 'Kantor ' . $sk . ', Paringin';
        $kotaAsal  = 'Paringin';

        $tempatKegiatan = trim((string) ($spt->tempat_kegiatan ?? ''));

        if ($spt->jenis_perjalanan === 'Dalam Daerah') {
            $kecamatanRaw  = trim((string) ($spt->kecamatan?->nama ?? ''));
            $kecamatanNama = $kecamatanRaw !== '' ? 'Kec. ' . $kecamatanRaw : '';
            $desaRaw       = trim((string) ($spt->desa ?? ''));
            $desaNama      = $desaRaw !== '' ? 'Desa ' . $desaRaw : '';
            $parts         = array_filter([$desaNama, $kecamatanNama], fn ($v) => $v !== '');
            $tujuan        = $parts ? implode(', ', $parts) : '-';

            // Split Row II - Dalam Daerah: Kiri=Kec, Kanan=Desa
            $tujuanTibaKiri  = $kecamatanNama !== '' ? $kecamatanNama : '-';
            $tujuanTibaKanan = $desaNama !== '' ? $desaNama : '-';
        } else {
            $kotaNama = $spt?->kotaTujuan?->nama ?? '';
            $parts    = array_filter([$kotaNama], fn ($v) => $v !== '');
            $tujuan   = $parts ? implode(', ', $parts) : '-';

            $tujuanTibaKiri  = $kotaNama !== '' ? $kotaNama : '-';
            $tujuanTibaKanan = $tempatKegiatan !== '' ? $tempatKegiatan : '-';
        }

        if ($tujuan === '') {
            $tujuan = '-';
        }

        $pangkatPG  = $pegawai?->pangkat ?? '';
        $golonganPG = $pegawai?->golongan ?? '';

        // Helper text fitting khusus item 5-10: hitung font-size agar muat dalam tinggi tetap, tanpa ubah konten/layout
        // Tidak menyentuh item 1-4. Hanya menyesuaikan font-size/line-height bila diperlukan.
        $fitStyle = function (string $text, float $heightMm, int $linesAvailable = 1, float $basePt = 11, float $minPt = 7) {
            $text = trim($text);
            if ($text === '' || $text === '-') {
                return '';
            }

            $len = mb_strlen($text);

            // Estimasi karakter per baris pada 11pt untuk lebar 56% (~110mm)
            $baseCharsPerLine = 42;
            $baseLineHeight   = 1.4;
            $minLineHeight    = 1.2;

            // Coba dari basePt turun ke minPt
            for ($pt = $basePt; $pt >= $minPt - 0.01; $pt -= 0.5) {
                $charsPerLine = (int) max(20, $baseCharsPerLine * 11 / $pt);
                $linesNeeded  = (int) ceil($len / $charsPerLine);

                // Hitung tinggi butuh, pakai line-height 1.4 dulu, jika di minPt coba 1.2
                $lh           = ($pt <= $minPt + 0.01) ? $minLineHeight : $baseLineHeight;
                $heightNeeded = $linesNeeded * $pt * $lh * 0.3528;

                // Sediakan sedikit buffer padding 1mm
                if ($heightNeeded <= ($heightMm - 1)) {
                    if (abs($pt - $basePt) < 0.01 && abs($lh - $baseLineHeight) < 0.01) {
                        return '';
                    }
                    return 'font-size:' . rtrim(rtrim(number_format($pt, 1, '.', ''), '0'), '.') . 'pt;line-height:' . $lh . ';';
                }
            }

            // Fallback min
            return 'font-size:' . rtrim(rtrim(number_format($minPt, 1, '.', ''), '0'), '.') . 'pt;line-height:' . $minLineHeight . ';';
        };
    @endphp

    <div class="nomor">Nomor : {{ $sppd->nomor_sppd ?? '-' }}</div>

    <div class="judul">
        <div class="judul-u">SURAT PERINTAH PERJALANAN DINAS</div>
        <div class="judul-s">(SPPD)</div>
    </div>

    <table class="tbl-depan">
        <tr>
            <td class="left-cell cell" style="height:13mm;">
                <table class="inner-num-table">
                    <tr>
                        <td class="num">1</td>
                        <td class="label-text">Pengguna Anggaran/Kuasa Pengguna Anggaran</td>
                    </tr>
                </table>
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
                <table class="inner-num-table">
                    <tr>
                        <td class="num">2</td>
                        <td class="label-text">Nama dan NIP Pegawai yang Diperintahkan</td>
                    </tr>
                </table>
            </td>
            <td class="right-cell cell" style="height:10mm;">{{ $pegawai?->nama ?? '-' }} / NIP. {{ $pegawai?->nip ?? '-' }}</td>
        </tr>
        <tr>
            <td class="left-cell cell" style="height:20mm;">
                <table class="inner-num-table">
                    <tr>
                        <td class="num">3</td>
                        <td class="label-text">
                            <div class="sub-line"><span class="sub-marker">a.</span><span class="sub-gap"></span>Pangkat dan Golongan</div>
                            <div class="sub-line"><span class="sub-marker">b.</span><span class="sub-gap"></span>Jabatan/Instansi</div>
                            <div class="sub-line"><span class="sub-marker">c.</span><span class="sub-gap"></span>Tingkat Biaya Perjalanan Dinas</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="right-cell cell" style="height:24mm;">
                <div class="sub-line"><span class="sub-marker">a.</span><span class="sub-gap"></span>{{ $pangkatPG }}@if($golonganPG) / {{ $golonganPG }}@endif</div>
                <div class="sub-line"><span class="sub-marker">b.</span><span class="sub-gap"></span>{{ $pegawai?->jabatan ?? '-' }} / {{ $pegawai?->unit_kerja ?? config('pejabat-sementara.instansi.nama') }}</div>
                <div class="sub-line"><span class="sub-marker">c.</span><span class="sub-gap"></span>-</div>
            </td>
        </tr>
        <tr>
            <td class="left-cell cell" style="height:11mm;">
                <table class="inner-num-table">
                    <tr>
                        <td class="num">4</td>
                        <td class="label-text">Maksud Perjalanan Dinas</td>
                    </tr>
                </table>
            </td>
            <td class="right-cell cell" style="height:11mm;">{{ $spt?->perihal ?? '-' }}</td>
        </tr>
        <tr>
            <td class="left-cell cell" style="height:7mm;">
                <table class="inner-num-table">
                    <tr>
                        <td class="num">5</td>
                        <td class="label-text">Alat angkut yang dipergunakan</td>
                    </tr>
                </table>
            </td>
            <td class="right-cell cell fit-cell" style="height:7mm;">
                <div class="fit-box fit-5" style="{{ $fitStyle('Kendaraan Dinas / Umum', 7, 1) }}">Kendaraan Dinas / Umum</div>
            </td>
        </tr>
        <tr>
            <td class="left-cell cell" style="height:16mm;">
                <table class="inner-num-table">
                    <tr>
                        <td class="num">6</td>
                        <td class="label-text">
                            <div class="sub-line"><span class="sub-marker">a.</span><span class="sub-gap"></span>Tempat berangkat</div>
                            <div class="sub-line"><span class="sub-marker">b.</span><span class="sub-gap"></span>Tempat tujuan</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="right-cell cell fit-cell" style="height:16mm;">
                <div class="fit-box fit-6" style="{{ $fitStyle($berangkat.' '.$tujuan, 16, 2) }}">
                    <div class="sub-line"><span class="sub-marker">a.</span><span class="sub-gap"></span>{{ $berangkat }}</div>
                    <div class="sub-line"><span class="sub-marker">b.</span><span class="sub-gap"></span>{{ $tujuan }}</div>
                </div>
            </td>
        </tr>
        <tr>
            <td class="left-cell cell" style="height:24mm;">
                <table class="inner-num-table">
                    <tr>
                        <td class="num">7</td>
                        <td class="label-text">
                            <div class="sub-line"><span class="sub-marker">a.</span><span class="sub-gap"></span>Lamanya Perjalanan Dinas</div>
                            <div class="sub-line"><span class="sub-marker">b.</span><span class="sub-gap"></span>Tanggal berangkat</div>
                            <div class="sub-line"><span class="sub-marker">c.</span><span class="sub-gap"></span>Tanggal harus kembali/tiba di tempat</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="right-cell cell fit-cell" style="height:24mm;">
                <div class="fit-box fit-7" style="{{ $fitStyle($lamaHari.' Hari '.$sppd->tanggal_berangkat?->translatedFormat('d F Y').' '.$sppd->tanggal_kembali?->translatedFormat('d F Y'), 24, 3) }}">
                    <div class="sub-line"><span class="sub-marker">a.</span><span class="sub-gap"></span>{{ $lamaHari }} Hari </div>
                    <div class="sub-line"><span class="sub-marker">b.</span><span class="sub-gap"></span>{{ $sppd->tanggal_berangkat?->translatedFormat('d F Y') ?? '-' }}</div>
                    <div class="sub-line"><span class="sub-marker">c.</span><span class="sub-gap"></span>{{ $sppd->tanggal_kembali?->translatedFormat('d F Y') ?? '-' }}</div>
                </div>
            </td>
        </tr>
        <tr>
            <td class="left-cell cell" style="height:20mm;">
                <table class="inner-num-table">
                    <tr>
                        <td class="num">8</td>
                        <td class="label-text">
                            <div class="sub-line">Pengikut : Nama</div>
                            <div class="sub-line"><span class="sub-marker">1.</span><span class="sub-gap"></span></div>
                            <div class="sub-line"><span class="sub-marker">2.</span><span class="sub-gap"></span></div>
                            <div class="sub-line"><span class="sub-marker">3.</span><span class="sub-gap"></span></div>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="right-cell cell fit-cell" style="height:28mm; padding:0;">
                <div class="fit-box fit-8" style="{{ $fitStyle('Tanggal Lahir Keterangan', 28, 5, 9.5, 7) }}">
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
                </div>
            </td>
        </tr>
        <tr>
            <td class="left-cell cell" style="height:24mm;">
                <table class="inner-num-table">
                    <tr>
                        <td class="num">9</td>
                        <td class="label-text">
                            <div class="sub-line">Pembebanan Anggaran</div>
                            <div class="sub-line"><span class="sub-marker">a.</span><span class="sub-gap"></span>SKPD</div>
                            <div class="sub-line"><span class="sub-marker">b.</span><span class="sub-gap"></span>Kode Rekening</div>
                        </td>
                    </tr>
                </table>
            </td>
            <td class="right-cell cell fit-cell" style="height:24mm;">
                <div class="fit-box fit-9" style="{{ $fitStyle(config('pejabat-sementara.instansi.nama'), 24, 3) }}">
                    <div class="sub-line">&nbsp;</div>
                    <div class="sub-line"><span class="sub-marker">a.</span><span class="sub-gap"></span>{{ config('pejabat-sementara.instansi.nama') }}</div>
                    <div class="sub-line"><span class="sub-marker">b.</span><span class="sub-gap"></span>-</div>
                </div>
            </td>
        </tr>
        <tr>
            <td class="left-cell cell" style="height:7mm;">
                <table class="inner-num-table">
                    <tr>
                        <td class="num">10</td>
                        <td class="label-text">Keterangan lain-lain</td>
                    </tr>
                </table>
            </td>
            <td class="right-cell cell fit-cell" style="height:7mm;">
                <div class="fit-box fit-10" style="{{ $fitStyle('-', 7, 1) }}">-</div>
            </td>
        </tr>
    </table>

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
            <div class="ttd-depan-jab">KEPALA DINAS KOMUNIKASI, INFORMATIKA,<br>STATISTIK DAN PERSANDIAN<br>KABUPATEN BALANGAN</div>
            <div class="ttd-depan-sp"></div>
            <div class="ttd-depan-nm">H. Syaifuddin Tailah, S.Pd, MM</div>
            <div class="ttd-depan-sub">Pembina Utama Muda (IV/c)</div>
            <div>NIP. 19670403 199403 1 015</div>
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
                <td class="blk-l" style="height:55mm;">
                    <table class="f-tbl">
                        <tr>
                            <td class="c-lab nowrap">II. Tiba di</td>
                            <td class="c-sep">:</td>
                            <td class="c-val">{{ $tujuanTibaKiri }}</td>
                        </tr>
                        <tr>
                            <td class="c-lab pada-tanggal">Pada Tanggal</td>
                            <td class="c-sep">:</td>
                            <td class="c-val">{{ $sppd->tanggal_berangkat?->translatedFormat('d F Y') ?? '-' }}</td>
                        </tr>
                    </table>
                    <div style="height:25mm;"></div>
                    <div class="dotted-line">.............................................</div>
                </td>
                <td class="blk-r" style="height:55mm;">
                    <table class="f-tbl">
                        <tr>
                            <td class="c-lab nowrap">Tiba di</td>
                            <td class="c-sep">:</td>
                            <td class="c-val">{{ $tujuanTibaKanan }}</td>
                        </tr>
                        <tr>
                            <td class="c-lab nowrap">Pada Tanggal</td>
                            <td class="c-sep">:</td>
                            <td class="c-val">{{ $sppd->tanggal_berangkat?->translatedFormat('d F Y') ?? '-' }}</td>
                        </tr>
                    </table>
                    <div style="height:25mm;"></div>
                    <div class="dotted-line">.............................................</div>
                </td>
            </tr>
            <tr>
                <td class="blk-l" style="height:55mm;">
                    <table class="f-tbl">
                        <tr>
                            <td class="c-lab nowrap">III. Tiba di</td>
                            <td class="c-sep">:</td>
                            <td class="c-val">&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="c-lab pada-tanggal-3">Pada Tanggal</td>
                            <td class="c-sep">:</td>
                            <td class="c-val">&nbsp;</td>
                        </tr>
                    </table>
                    <div style="height:25mm;"></div>
                    <div class="dotted-line">.............................................</div>
                </td>
                <td class="blk-r" style="height:36mm;">
                    <table class="f-tbl">
                        <tr>
                            <td class="c-lab nowrap">Tiba di</td>
                            <td class="c-sep">:</td>
                            <td class="c-val">&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="c-lab nowrap">Pada Tanggal</td>
                            <td class="c-sep">:</td>
                            <td class="c-val">&nbsp;</td>
                        </tr>
                    </table>
                    <div style="height:25mm;"></div>
                    <div class="dotted-line">.............................................</div>
                </td>
            </tr>
            <tr>
                <td class="blk-l" style="height:64mm;">
                    <table class="f-tbl">
                        <tr>
                            <td class="c-lab nowrap">IV. Tiba di</td>
                            <td class="c-sep">:</td>
                            <td class="c-val">{{ $kotaAsal }}</td>
                        </tr>
                        <tr>
                            <td class="c-lab pada-tanggal-3">Pada Tanggal</td>
                            <td class="c-sep">:</td>
                            <td class="c-val">{{ $sppd->tanggal_kembali?->translatedFormat('d F Y') ?? '-' }}</td>
                        </tr>
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

    <script>
    // Text fitting runtime khusus item 5-10: kecilkan font/line-height hanya jika overflow, tinggi cell tetap
    (function () {
        if (typeof window === 'undefined' || typeof document === 'undefined') return;

        // Hanya untuk preview browser, Dompdf mengabaikan JS tapi PHP sudah handle
        function fitEl(el) {
            // el adalah .fit-box, parent adalah td.fit-cell dengan height fixed
            var parent = el.parentElement;
            if (!parent) return;

            var maxH = parent.clientHeight;
            // Jika parent belum ter-render dengan height fixed, pakai offsetHeight
            if (!maxH) maxH = el.clientHeight;

            var style = window.getComputedStyle(el);
            var fontSize = parseFloat(style.fontSize);
            var minSize = 7;
            var lineHeight = parseFloat(style.lineHeight) || fontSize * 1.4;
            var iter = 0;

            while (el.scrollHeight > maxH && fontSize > minSize && iter < 20) {
                fontSize = Math.max(minSize, fontSize - 0.5);
                el.style.fontSize = fontSize + 'px';
                // Minimal line-height 1.2 jika masih overflow
                if (el.scrollHeight > maxH && fontSize <= 8) {
                    el.style.lineHeight = '1.2';
                }
                iter++;
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.fit-box').forEach(function (el) {
                // Lewati jika kosong atau sudah muat
                if (!el.textContent.trim()) return;
                fitEl(el);
            });
        });

        // Juga coba saat load untuk Dompdf preview iframe
        if (document.readyState !== 'loading') {
            document.querySelectorAll('.fit-box').forEach(function (el) {
                if (!el.textContent.trim()) return;
                // delay sedikit agar layout selesai
                setTimeout(function () { fitEl(el); }, 50);
            });
        }
    })();
    </script>
</body>
</html>