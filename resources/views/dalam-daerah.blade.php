@extends('layouts.app')

@section('title', 'Dalam Daerah - SPT & SPPD')

@section('content')

    <div class="page-header">
        <h1>Dalam Daerah</h1>
        <p>Data perjalanan dinas dalam wilayah Kabupaten Balangan.</p>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">

        <div class="card-header">
            <h2>Data SPT & SPPD Dalam Daerah</h2>
        </div>

        <div class="table-wrapper">

            <table>

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nomor SPT</th>
                        <th>Nomor SPPD</th>
                        <th>Nama Pegawai</th>
                        <th>Tujuan</th>
                        <th>Tanggal Berangkat</th>
                        <th>Tanggal Kembali</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($data as $index => $item)

                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>

                            <td>
                                {{ $item->spt?->nomor_spt ?? '-' }}
                            </td>

                            <td>
                                {{ $item->nomor_sppd ?? '-' }}
                            </td>

                            <td>
                                {{ $item->pegawai?->nama ?? '-' }}
                            </td>

                            <td>
                                {{ $item->spt?->kecamatan?->nama ?? '-' }}
                                @if($item->spt?->desa)
                                    <br>
                                    <small>{{ $item->spt->desa }}</small>
                                @endif
                            </td>

                            <td>
                                {{ $item->tanggal_berangkat?->translatedFormat('d M Y') ?? '-' }}
                            </td>

                            <td>
                                {{ $item->tanggal_kembali?->translatedFormat('d M Y') ?? '-' }}
                            </td>

                            <td>

                                <div class="row-actions">

                                    <a
                                        href="{{ route('form.edit', $item) }}"
                                        class="btn-edit"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        action="{{ route('form.delete', $item) }}"
                                        method="POST"
                                        onsubmit="return confirm('Hapus data perjalanan ini beserta SPPD terkait?');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn-danger">
                                            Hapus
                                        </button>
                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" class="empty">
                                Belum ada data perjalanan dinas dalam daerah.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

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
        min-width: 850px;
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

    .row-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .row-actions form {
        margin: 0;
    }

    .btn-edit,
    .btn-danger {
        display: inline-block;
        border: none;
        border-radius: 6px;
        padding: 7px 13px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-edit {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .btn-edit:hover {
        background: #bfdbfe;
    }

    .btn-danger {
        background: #fee2e2;
        color: #b91c1c;
    }

    .btn-danger:hover {
        background: #fecaca;
    }

    td small {
        color: #6b7280;
    }

</style>

@endpush