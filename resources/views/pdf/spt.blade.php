<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Surat Perintah Tugas</title>

    <style>

        @page {
            size: A4 portrait;
            margin: 20mm 18mm 18mm 20mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 11pt;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .kop {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }

        .kop-logo {
            width: 85px;
            text-align: center;
            vertical-align: middle;
        }

        .kop-logo img {
            display: block;
            width: 65px;
            height: auto;
            margin: 0 auto;
        }

        .kop-text {
            text-align: center;
            vertical-align: middle;
            padding-right: 50px;
        }

        .kop-pemda {
            font-size: 15pt;
            font-weight: bold;
            line-height: 1.1;
        }

        .kop-dinas {
            font-size: 14pt;
            font-weight: bold;
            line-height: 1.15;
            margin-top: 2px;
        }

        .kop-alamat {
            font-size: 9pt;
            margin-top: 5px;
            line-height: 1.3;
        }

        .judul {
            text-align: center;
            margin-top: 8px;
        }

        .judul-utama {
            font-size: 15pt;
            font-weight: bold;
            text-decoration: underline;
        }

        .nomor {
            margin-top: 5px;
            font-size: 11pt;
        }

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

        .detail {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .detail td {
            vertical-align: top;
            padding: 3px 0;
        }

        .detail-label {
            width: 80px;
            font-weight: bold;
        }

        .detail-titik {
            width: 20px;
        }

        .penutup {
            margin-top: 15px;
            line-height: 1.5;
        }

        .ttd-wrapper {
            width: 100%;
            margin-top: 15px;
        }

        .ttd {
            width: 48%;
            margin-left: auto;
            text-align: left;
        }

        .ttd-table {
            width: 100%;
            border-collapse: collapse;
        }

        .ttd-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .ttd-label {
            width: 105px;
            font-weight: bold;
        }

        .ttd-titik {
            width: 15px;
        }

        .kepala-dinas {
            text-align: center;
            margin-top: 15px;
        }

        .ruang-tanda-tangan {
            height: 65px;
        }

        .nama-kepala {
            font-weight: bold;
            text-decoration: underline;
        }

        .jabatan-kepala {
            margin-top: 2px;
        }

        .nip {
            margin-top: 2px;
        }

    </style>

</head>

<body>


{{-- ================================================= --}}
{{-- KOP SURAT --}}
{{-- ================================================= --}}

<div class="kop">

    <table class="kop-table">

        <tr>

            <td class="kop-logo">

                @if(file_exists($logoPath))

                    <img
                        src="{{ $logoPath }}"
                        alt="Logo Kabupaten Balangan"
                    >

                @endif

            </td>


            <td class="kop-text">

                <div class="kop-pemda">
                    PEMERINTAH KABUPATEN BALANGAN
                </div>

                <div class="kop-dinas">
                    DINAS KOMUNIKASI INFORMATIKA,<br>
                    STATISTIK DAN PERSANDIAN
                </div>

                <div class="kop-alamat">
                    Jalan Jenderal Ahmad Yani Km. 3,5 Telp/Fax. (0526) 2028434
                    Kec. Paringin Selatan<br>

                    Website : www.diskominfo.balangankab.go.id /
                    Email : diskominfo@balangankab.go.id
                </div>

            </td>

        </tr>

    </table>

</div>


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
{{-- DATA PEGAWAI --}}
{{-- ================================================= --}}

<div class="pegawai-wrapper">

    @foreach($spt->pegawais as $index => $pegawai)

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
{{-- TANDA TANGAN --}}
{{-- ================================================= --}}

<div class="ttd-wrapper">

    <div class="ttd">

        <table class="ttd-table">

            <tr>

                <td class="ttd-label">
                    Ditetapkan di
                </td>

                <td class="ttd-titik">
                    :
                </td>

                <td>
                    Paringin Selatan
                </td>

            </tr>


            <tr>

                <td class="ttd-label">
                    Pada Tanggal
                </td>

                <td class="ttd-titik">
                    :
                </td>

                <td>
                    {{ $spt->tanggal_spt?->translatedFormat('d F Y') }}
                </td>

            </tr>

        </table>


        <div class="kepala-dinas">

            Kepala Dinas

            <div class="ruang-tanda-tangan"></div>

            <div class="nama-kepala">
                H. Syaifuddin Tailah, S.Pd, MM
            </div>

            <div class="jabatan-kepala">
                Pembina Utama Muda (IV/c)
            </div>

            <div class="nip">
                NIP. 19670403 199403 1 015
            </div>

        </div>

    </div>

</div>


</body>

</html>