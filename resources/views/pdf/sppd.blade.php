<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Surat Perjalanan Dinas</title>

    @include('pdf.partials.styles')

    <style>

        .data-sppd {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }

        .data-sppd td {
            vertical-align: top;
            padding: 4px 0;
        }

        .data-no {
            width: 30px;
        }

        .data-label {
            width: 170px;
        }

        .data-titik {
            width: 15px;
        }

        .catatan-kosong {
            margin-top: 20px;
            font-size: 9pt;
            font-style: italic;
            color: #444;
        }

    </style>

</head>

<body>


{{-- ================================================= --}}
{{-- KOP SURAT --}}
{{-- ================================================= --}}

@include('pdf.partials.kop')


{{-- ================================================= --}}
{{-- JUDUL --}}
{{-- ================================================= --}}

<div class="judul">

    <div class="judul-utama">
        SURAT PERJALANAN DINAS
    </div>

    <div class="nomor">
        Nomor : {{ $sppd->nomor_sppd ?? '-' }}
    </div>

</div>


{{-- ================================================= --}}
{{-- DATA PEGAWAI YANG BERPERJALANAN --}}
{{-- ================================================= --}}

<table class="data-sppd">

    <tr>
        <td class="data-no">1.</td>
        <td class="data-label">Nama</td>
        <td class="data-titik">:</td>
        <td>{{ $pegawai?->nama ?: '-' }}</td>
    </tr>


    <tr>
        <td></td>
        <td class="data-label">NIP</td>
        <td class="data-titik">:</td>
        <td>{{ $pegawai?->nip ?: '-' }}</td>
    </tr>


    <tr>
        <td></td>
        <td class="data-label">Pangkat/Golongan</td>
        <td class="data-titik">:</td>
        <td>
            {{ $pegawai?->pangkat ?: '-' }}

            @if($pegawai?->golongan)
                / {{ $pegawai->golongan }}
            @endif
        </td>
    </tr>


    <tr>
        <td></td>
        <td class="data-label">Jabatan</td>
        <td class="data-titik">:</td>
        <td>{{ $pegawai?->jabatan ?: '-' }}</td>
    </tr>


    <tr>
        <td></td>
        <td class="data-label">Unit Kerja</td>
        <td class="data-titik">:</td>
        <td>{{ $pegawai?->unit_kerja ?: '-' }}</td>
    </tr>


    {{-- Field administratif yang belum punya sumber data:
         dibiarkan placeholder, jangan diisi asal. --}}

    <tr>
        <td></td>
        <td class="data-label">Tingkat Biaya</td>
        <td class="data-titik">:</td>
        <td>-</td>
    </tr>


    <tr>
        <td></td>
        <td class="data-label">Alat Angkutan</td>
        <td class="data-titik">:</td>
        <td>-</td>
    </tr>


    <tr>
        <td></td>
        <td class="data-label">Tempat Berangkat</td>
        <td class="data-titik">:</td>
        <td>-</td>
    </tr>


    <tr>
        <td></td>
        <td class="data-label">Tempat Tujuan</td>
        <td class="data-titik">:</td>
        <td>

            @if($spt?->jenis_perjalanan === 'Dalam Daerah')

                @if($spt->desa)
                    Desa {{ $spt->desa }}
                @endif

                @if($spt->kecamatan)
                    Kec. {{ $spt->kecamatan->nama }}
                @endif

            @else

                {{ $spt?->kotaTujuan?->nama ?? '-' }}

            @endif

        </td>
    </tr>


    <tr>
        <td></td>
        <td class="data-label">Untuk</td>
        <td class="data-titik">:</td>
        <td>{{ $spt?->perihal ?: '-' }}</td>
    </tr>


    <tr>
        <td></td>
        <td class="data-label">Lama Perjalanan</td>
        <td class="data-titik">:</td>
        <td>
            @if($lamaHari)
                {{ $lamaHari }} Hari
            @else
                -
            @endif
        </td>
    </tr>


    <tr>
        <td></td>
        <td class="data-label">Tanggal Berangkat</td>
        <td class="data-titik">:</td>
        <td>{{ $sppd->tanggal_berangkat?->translatedFormat('d F Y') ?? '-' }}</td>
    </tr>


    <tr>
        <td></td>
        <td class="data-label">Tanggal Kembali</td>
        <td class="data-titik">:</td>
        <td>{{ $sppd->tanggal_kembali?->translatedFormat('d F Y') ?? '-' }}</td>
    </tr>

</table>


{{-- ================================================= --}}
{{-- PENUTUP --}}
{{-- ================================================= --}}

<div class="penutup">

    Demikian Surat Perjalanan Dinas ini diberikan untuk dilaksanakan dengan penuh tanggung jawab.

</div>


{{-- ================================================= --}}
{{-- TANDA TANGAN (DINAMIS SESUAI PENANDATANGAN) --}}
{{-- ================================================= --}}

@include('pdf.partials.ttd', [
    'penandatangan' => $penandatangan,
    'tanggalTtd' => now(),
])


</body>

</html>
