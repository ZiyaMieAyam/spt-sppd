<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Perintah Tugas</title>
    @include('pdf.partials.styles')
    <style>
        @page {
            size: 215mm 330mm;
            margin: 5mm 8mm 5mm 8mm;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            margin: 0;
            padding: 0;
            color: #000;
        }

        /* ==== KOP (samakan dengan SPPD) ==== */
        .kop {
            padding-bottom: 4px;
            margin-bottom: 5mm;
            border-bottom: 2px double #000;
        }

        .kop-pemda {
            font-size: 12pt;
            line-height: 1.0;
        }

        .kop-dinas {
            font-size: 15pt;
            line-height: 1.1;
            margin-top: 1px;
        }

        .kop-alamat {
            font-size: 8pt;
            margin-top: 2px;
            line-height: 1.2;
        }

        .kop-logo img {
            width: 46px;
        }

        /* ==== JUDUL ==== */
        .judul {
            text-align: center;
            margin: 10px 0 11px;
        }

        .judul-utama {
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .nomor {
            font-size: 11pt;
            margin-top: 3px;
        }

        /* ==== MENUGASKAN ==== */
        .menugaskan {
            text-align: center;
            font-weight: bold;
            letter-spacing: 5px;
            margin-top: 15px;
            margin-bottom: 10px;
        }

        /* ==== KEPADA ==== */
        .kepada {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
        }

        .kepada td {
            vertical-align: top;
            padding: 2px 0;
            font-size: 11pt;
        }

        .label-kepada {
            width: 80px;
            font-weight: bold;
        }

        .titik {
            width: 20px;
        }

        /* ==== DATA PEGAWAI ==== */
        .pegawai-wrapper {
            margin-top: 4px;
            margin-left: 105px;
        }

        .pegawai {
            margin-bottom: 8px;
        }

        .pegawai-table {
            width: 100%;
            border-collapse: collapse;
        }

        .pegawai-table td {
            vertical-align: top;
            padding: 1px 0;
            font-size: 11pt;
        }

        .pegawai-nomor {
            width: 25px;
        }

        .pegawai-label {
            width: 70px;
        }

        .pegawai-titik {
            width: 15px;
        }

        .pegawai-nama {
            font-weight: bold;
        }

        /* ==== DASAR ==== */
        .dasar-table td {
            vertical-align: top;
            padding: 2px 0;
            font-size: 11pt;
        }

        .dasar-label {
            width: 80px;
            font-weight: bold;
            padding-left: 12px;
        }

        .dasar-titik {
            width: 20px;
            padding-left: 4px;
            padding-right: 4px;
        }

        .dasar-content {
            line-height: 1.5;
            white-space: pre-wrap;
            padding-right: 12px;
        }

        /* ==== DETAIL TUGAS ==== */
        .detail {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .detail td {
            vertical-align: top;
            padding: 2px 0;
            font-size: 11pt;
        }

        .detail-label {
            width: 80px;
            font-weight: bold;
        }

        .detail-titik {
            width: 20px;
        }

        /* ==== PENUTUP ==== */
        .penutup {
            margin-top: 12px;
            line-height: 1.4;
        }
    </style>
</head>

<body>
    @include('pdf.partials.kop')

    {{-- Judul --}}
    <div class="judul">
        <div class="judul-utama">SURAT PERINTAH TUGAS</div>
        <div class="nomor">Nomor : {{ $spt->nomor_spt }}</div>
    </div>

    @if (!empty($spt->dasar))
        <table class="dasar-table">
            <tr>
                <td class="dasar-label">Dasar</td>
                <td class="dasar-titik">:</td>
                <td class="dasar-content">{!! nl2br(e($spt->dasar)) !!}</td>
            </tr>
        </table>
    @endif

    {{-- Menugaskan --}}
    <div class="menugaskan">M E N U G A S K A N :</div>

    {{-- Kepada --}}
    <table class="kepada">
        <tr>
            <td class="label-kepada">Kepada</td>
            <td class="titik">:</td>
            <td></td>
        </tr>
    </table>

    {{-- Data pegawai --}}
    <div class="pegawai-wrapper">
        @foreach ($pegawais as $index => $pegawai)
            <div class="pegawai">
                <table class="pegawai-table">
                    <tr>
                        <td class="pegawai-nomor">{{ $index + 1 }}.</td>
                        <td class="pegawai-label">Nama</td>
                        <td class="pegawai-titik">:</td>
                        <td class="pegawai-nama">{{ $pegawai->nama }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>NIP</td>
                        <td>:</td>
                        <td>{{ $pegawai->nip ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Pangkat/Gol</td>
                        <td>:</td>
                        <td>
                            {{ $pegawai->pangkat ?: '-' }}
                            @if ($pegawai->golongan)
                                / {{ $pegawai->golongan }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>Jabatan</td>
                        <td>:</td>
                        <td>{{ $pegawai->jabatan ?: '-' }}</td>
                    </tr>
                </table>
            </div>
        @endforeach
    </div>

    {{-- Detail tugas --}}
    <table class="detail">
        <tr>
            <td class="detail-label">Untuk</td>
            <td class="detail-titik">:</td>
            <td>{{ $spt->perihal }}</td>
        </tr>
        <tr>
            <td class="detail-label">Tempat</td>
            <td class="detail-titik">:</td>
            <td>
                @php
                    $tk = trim((string) ($spt->tempat_kegiatan ?? ''));

                    if ($spt->jenis_perjalanan === 'Dalam Daerah') {
                        $des = $spt->desa ? 'Desa ' . $spt->desa : '';
                        $kec = $spt->kecamatan?->nama ? 'Kec. ' . $spt->kecamatan->nama : '';
                        $p = array_filter([$des, $kec], fn ($v) => $v !== '');
                        echo $p ? implode(', ', $p) : '-';
                    } else {
                        $kota = $spt->kotaTujuan?->nama ?? '';
                        $p = array_filter([$kota], fn ($v) => $v !== '');
                        echo $p ? implode(' - ', $p) : '-';
                    }
                @endphp
            </td>
        </tr>
        <tr>
            <td class="detail-label">Tanggal</td>
            <td class="detail-titik">:</td>
            <td>{{ $spt->tanggal_berangkat?->translatedFormat('d F') }} s.d {{ $spt->tanggal_kembali?->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    {{-- Penutup --}}
    <div class="penutup">Demikian Surat Tugas ini diberikan agar digunakan sebagaimana mestinya.</div>

    {{-- Tanda tangan --}}
    @include('pdf.partials.ttd', [
        'penandatangan' => $penandatangan,
        'tanggalTtd' => $spt->tanggal_spt,
    ])
</body>
</html>