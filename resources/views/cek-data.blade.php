<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data SPT & SPPD</title>

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family:'Segoe UI',sans-serif;
            background:#f1f5f9;
            color:#334155;
            padding:40px;
        }

        .container{
            max-width:1400px;
            margin:auto;
        }

        h1{
            font-size:34px;
            margin-bottom:30px;
            color:#1e293b;
        }

        .card{
            background:#fff;
            border-radius:18px;
            overflow:hidden;
            box-shadow:0 10px 25px rgba(0,0,0,.08);
            margin-bottom:35px;
        }

        .card-header{
            background:#2563eb;
            color:white;
            padding:18px 25px;
            font-size:22px;
            font-weight:bold;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        th{
            background:#f8fafc;
            padding:15px;
            text-align:left;
            border-bottom:2px solid #e2e8f0;
            color:#334155;
            font-size:14px;
        }

        td{
            padding:15px;
            border-bottom:1px solid #e5e7eb;
            vertical-align:top;
        }

        tbody tr:nth-child(even){
            background:#fafafa;
        }

        tbody tr:hover{
            background:#f8fbff;
            transition:.2s;
        }

        .badge{
            display:inline-block;
            padding:6px 14px;
            border-radius:50px;
            color:white;
            font-size:12px;
            font-weight:bold;
        }

        .green{
            background:#16a34a;
        }

        .red{
            background:#dc2626;
        }

        .pegawai{
            display:inline-block;
            padding:6px 12px;
            margin:3px;
            border-radius:30px;
            background:#dbeafe;
            color:#1d4ed8;
            font-size:13px;
            font-weight:600;
        }

        .nomor{
            font-weight:bold;
            color:#1e40af;
        }

        .kosong{
            text-align:center;
            padding:40px;
            color:#64748b;
        }

        small{
            color:#64748b;
        }

    </style>

</head>

<body>

<div class="container">

    <h1>📄 Data Surat Perintah Tugas & Surat Perintah Perjalanan Dinas</h1>

    {{-- ======================== SPT ========================= --}}

    <div class="card">

        <div class="card-header">
            Data Surat Perintah Tugas (SPT)
        </div>

        <table>

            <thead>

            <tr>

                <th width="60">No</th>

                <th>Nomor SPT</th>

                <th>Pegawai</th>

                <th>Jenis</th>

                <th>Tanggal</th>

                <th>Tujuan</th>

                <th>Perihal</th>

            </tr>

            </thead>

            <tbody>

            @forelse($spts as $spt)

                <tr>

                    <td>{{ $loop->iteration }}</td>

                    <td class="nomor">

                        {{ $spt->nomor_spt }}

                    </td>

                    <td>

                        @forelse($spt->pegawais as $pegawai)

                            <span class="pegawai">

                                {{ $pegawai->nama }}

                            </span>

                        @empty

                            -

                        @endforelse

                    </td>

                    <td>

                        @if($spt->jenis_perjalanan=="Dalam Daerah")

                            <span class="badge green">

                                Dalam Daerah

                            </span>

                        @else

                            <span class="badge red">

                                Luar Daerah

                            </span>

                        @endif

                    </td>

                    <td>

                        {{ \Carbon\Carbon::parse($spt->tanggal_spt)->format('d M Y') }}

                    </td>

                    <td>

                        @if($spt->jenis_perjalanan=="Dalam Daerah")

                            <strong>{{ $spt->kecamatan->nama ?? '-' }}</strong>

                            <br>

                            <small>{{ $spt->desa }}</small>

                        @else

                            {{ $spt->kotaTujuan->nama ?? '-' }}

                        @endif

                    </td>

                    <td>

                        {{ $spt->perihal }}

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="7" class="kosong">

                        Belum ada data SPT.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

    {{-- ======================== SPPD ========================= --}}

    <div class="card">

        <div class="card-header">

            Data Surat Perintah Perjalanan Dinas (SPPD)

        </div>

        <table>

            <thead>

            <tr>

                <th width="60">No</th>

                <th>Nomor SPPD</th>

                <th>Nomor SPT</th>

                <th>Pegawai</th>

            </tr>

            </thead>

            <tbody>

            @forelse($sppds as $sppd)

                <tr>

                    <td>{{ $loop->iteration }}</td>

                    <td class="nomor">

                        {{ $sppd->nomor_sppd }}

                    </td>

                    <td>

                        {{ $sppd->spt->nomor_spt ?? '-' }}

                    </td>

                    <td>

                        <span class="pegawai">

                            {{ $sppd->pegawai->nama ?? '-' }}

                        </span>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="4" class="kosong">

                        Belum ada data SPPD.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

</div>

</body>
</html>