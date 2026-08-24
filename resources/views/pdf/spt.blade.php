<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Surat Perintah Tugas</title>

    @include('pdf.partials.styles')

    <style>

        .menugaskan {
            text-align: center;
            font-weight: bold;
            letter-spacing: 5px;
            margin-top: 15px;
            margin-bottom: 10px;
        }

        .kepada {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
        }

        .kepada td {
            vertical-align: top;
            padding: 2px 0;
        }

        .label-kepada {
            width: 80px;
            font-weight: bold;
        }

        .titik {
            width: 20px;
        }

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
        SURAT PERINTAH TUGAS
    </div>

    <div class="nomor">
        Nomor : {{ $spt->nomor_spt }}
    </div>

</div>


{{-- ================================================= --}}
{{-- MENUGASKAN --}}
{{-- ================================================= --}}

<div class="menugaskan">

    M E N U G A S K A N :

</div>


{{-- ================================================= --}}
{{-- KEPADA --}}
{{-- ================================================= --}}

<table class="kepada">

    <tr>

        <td class="label-kepada">
            Kepada
        </td>

        <td class="titik">
            :
        </td>

        <td>
        </td>

    </tr>

</table>


{{-- ================================================= --}}
{{-- DATA PEGAWAI (TERURUT GOLONGAN -> JABATAN) --}}
{{-- ================================================= --}}

<div class="pegawai-wrapper">

    @foreach($pegawais as $index => $pegawai)

        <div class="pegawai">

            <table class="pegawai-table">

                <tr>

                    <td class="pegawai-nomor">
                        {{ $index + 1 }}.
                    </td>

                    <td class="pegawai-label">
                        Nama
                    </td>

                    <td class="pegawai-titik">
                        :
                    </td>

                    <td class="pegawai-nama">
                        {{ $pegawai->nama }}
                    </td>

                </tr>


                <tr>

                    <td></td>

                    <td>
                        NIP
                    </td>

                    <td>
                        :
                    </td>

                    <td>
                        {{ $pegawai->nip ?: '-' }}
                    </td>

                </tr>


                <tr>

                    <td></td>

                    <td>
                        Pangkat/Gol
                    </td>

                    <td>
                        :
                    </td>

                    <td>

                        {{ $pegawai->pangkat ?: '-' }}

                        @if($pegawai->golongan)
                            / {{ $pegawai->golongan }}
                        @endif

                    </td>

                </tr>


                <tr>

                    <td></td>

                    <td>
                        Jabatan
                    </td>

                    <td>
                        :
                    </td>

                    <td>
                        {{ $pegawai->jabatan ?: '-' }}
                    </td>

                </tr>

            </table>

        </div>

    @endforeach

</div>


{{-- ================================================= --}}
{{-- DETAIL TUGAS --}}
{{-- ================================================= --}}

<table class="detail">

    <tr>

        <td class="detail-label">
            Untuk
        </td>

        <td class="detail-titik">
            :
        </td>

        <td>
            {{ $spt->perihal }}
        </td>

    </tr>


    <tr>

        <td class="detail-label">
            Tempat
        </td>

        <td class="detail-titik">
            :
        </td>

        <td>

            @if($spt->jenis_perjalanan === 'Dalam Daerah')

                @if($spt->desa)
                    Desa {{ $spt->desa }}
                @endif

                @if($spt->kecamatan)
                    Kec. {{ $spt->kecamatan->nama }}
                @endif

            @else

                {{ $spt->kotaTujuan?->nama ?? '-' }}

            @endif

        </td>

    </tr>


    <tr>

        <td class="detail-label">
            Tanggal
        </td>

        <td class="detail-titik">
            :
        </td>

        <td>
            {{ $spt->tanggal_berangkat?->translatedFormat('d F Y') }}
        </td>

    </tr>

</table>


{{-- ================================================= --}}
{{-- PENUTUP --}}
{{-- ================================================= --}}

<div class="penutup">

    Demikian Surat Tugas ini diberikan agar digunakan sebagaimana mestinya.

</div>


{{-- ================================================= --}}
{{-- TANDA TANGAN (DINAMIS SESUAI PENANDATANGAN) --}}
{{-- ================================================= --}}

@include('pdf.partials.ttd', [
    'penandatangan' => $penandatangan,
    'tanggalTtd' => $spt->tanggal_spt,
])


</body>

</html>
