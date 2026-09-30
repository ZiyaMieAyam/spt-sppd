@extends('layouts.app')

@section('title', 'Luar Daerah - SiPerjadin')

@section('content')

    <div class="page-header">
        <h1>Luar Daerah</h1>
        <p>Data perjalanan dinas ke luar wilayah Kabupaten Balangan.</p>
    </div>

    @if (session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">

        <div class="card-header">
            <h2>Data SPT & SPPD Luar Daerah</h2>
        </div>

        <div class="table-wrapper">

            <table>

                <thead>
                    <tr>
                        <th class="col-no">No</th>
                        <th>Nomor SPT</th>
                        <th>Nomor SPPD</th>
                        <th>Nama Pegawai</th>
                        <th>Tujuan</th>
                        <th>Tanggal Berangkat</th>
                        <th>Tanggal Kembali</th>
                        <th class="col-aksi">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @php($sptIdTerakhir = null)
                    @forelse ($data as $index => $item)

                        <tr>

                            <td class="col-no">{{ ($data->firstItem() ?? 0) + $index }}</td>

                            <td>{{ $item->spt?->nomor_spt ?? '-' }}</td>

                            <td>{{ $item->nomor_sppd ?? '-' }}</td>

                            <td>{{ $item->pegawai?->nama ?? '-' }}</td>

                            <td>{{ $item->spt?->kotaTujuan?->nama ?? '-' }}</td>

                            <td>{{ $item->tanggal_berangkat?->translatedFormat('d M Y') ?? '-' }}</td>

                            <td>{{ $item->tanggal_kembali?->translatedFormat('d M Y') ?? '-' }}</td>

                            <td class="col-aksi">

                                <div class="aksi-grup">

                                    @if ($item->spt)

                                        <a href="{{ route('spts.pdf.pilih', $item->spt->id) }}" target="_blank" class="btn-print" title="Cetak PDF SPT">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M6 9V2h12v7"/>
                                                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                                                <rect x="6" y="14" width="12" height="8"/>
                                            </svg>
                                            Cetak SPT
                                        </a>

                                    @endif

                                    <a href="{{ route('sppds.pdf', $item->id) }}" target="_blank" class="btn-print btn-sppd" title="Cetak PDF SPPD">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M6 9V2h12v7"/>
                                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                                            <rect x="6" y="14" width="12" height="8"/>
                                        </svg>
                                        Cetak SPPD
                                    </a>

                                    @if (auth()->user()?->isAdmin() && $item->spt && $item->spt->id !== $sptIdTerakhir)
                                        <form action="{{ route('form.delete', $item->spt->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Yakin ingin menghapus SPT ini beserta seluruh SPPD yang terkait?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete" title="Hapus SPT beserta seluruh SPPD">
                                                Hapus SPT
                                            </button>
                                        </form>
                                    @endif
                                    @php($sptIdTerakhir = $item->spt?->id)

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" class="empty">
                                Belum ada data perjalanan dinas luar daerah.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        @if ($data->hasPages())
            <div class="pagination-wrapper">
                {{ $data->links() }}
            </div>
        @endif

    </div>

@endsection

@push('styles')
    <style>
        .card {
            overflow: hidden;
        }

        .card-header {
            padding: 20px 25px;
            border-bottom: 1px solid #e5e7eb;
        }

        .card-header h2 {
            margin: 0;
            font-size: 18px;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f9fafb;
            color: #374151;
            font-size: 13px;
            text-align: left;
            padding: 13px 15px;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        td {
            padding: 13px 15px;
            font-size: 13px;
            border-bottom: 1px solid #f3f4f6;
            white-space: nowrap;
        }

        .col-no {
            width: 50px;
            text-align: center;
        }

        tbody tr {
            transition: background 0.15s ease;
        }

        tbody tr:hover {
            background: #f9fafb;
        }

        .empty {
            text-align: center;
            color: #9ca3af;
            padding: 35px;
        }

        .alert-success {
            background: #dcfce7;
            border: 1px solid #bbf7d0;
            color: #166534;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .col-aksi {
            width: 1%;
            white-space: nowrap;
            text-align: right;
        }

        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #2563eb;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 8px;
            text-decoration: none;
            white-space: nowrap;
            transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
        }

        .btn-print:hover {
            background: #dbeafe;
            border-color: #93c5fd;
        }

        .aksi-grup {
            display: inline-flex;
            gap: 8px;
        }

        .btn-sppd {
            color: #15803d;
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .btn-sppd:hover {
            background: #dcfce7;
            border-color: #86efac;
        }

        .btn-delete {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #b91c1c;
            background: #fef2f2;
            border: 1px solid #fecaca;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 8px;
            white-space: nowrap;
            cursor: pointer;
        }

        .btn-delete:hover {
            background: #fee2e2;
            border-color: #fca5a5;
        }

        .pagination-wrapper {
            padding: 16px 25px;
            border-top: 1px solid #e5e7eb;
        }
    </style>
@endpush