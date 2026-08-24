@extends('layouts.app')

@section('title', 'Cetak PDF - SiPerjadin')

@section('content')

    <div class="page-header">
        <h1>Cetak PDF</h1>
        <p>Pilih penandatangan sebelum mencetak dokumen PDF.</p>
    </div>

    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    <div class="card">

        <div class="card-header">
            <h2>{{ $sppd ? 'Cetak SPPD' : 'Cetak SPT' }}</h2>
        </div>

        <div class="card-body">

            <div class="info-dokumen">

                <div class="info-row">
                    <span class="info-label">Nomor {{ $sppd ? 'SPPD' : 'SPT' }}</span>
                    <span class="info-nilai">{{ $sppd ? ($sppd->nomor_sppd ?? '-') : $spt->nomor_spt }}</span>
                </div>

                @if($sppd)
                    <div class="info-row">
                        <span class="info-label">Pegawai</span>
                        <span class="info-nilai">{{ $sppd->pegawai?->nama ?? '-' }}</span>
                    </div>
                @endif

                <div class="info-row">
                    <span class="info-label">Nomor SPT</span>
                    <span class="info-nilai">{{ $spt->nomor_spt }}</span>
                </div>

                <div class="info-row">
                    <span class="info-label">Pegawai Ditugaskan</span>
                    <span class="info-nilai">{{ $pegawais->count() }} orang</span>
                </div>

            </div>

            @if($pegawais->isNotEmpty())

                <div class="daftar-pegawai">

                    <h3>Daftar Pegawai (urut golongan)</h3>

                    <ol>
                        @foreach($pegawais as $pegawai)
                            <li>
                                <strong>{{ $pegawai->nama }}</strong>
                                &mdash; {{ $pegawai->pangkat ?: '-' }}/{{ $pegawai->golongan ?: '-' }},
                                {{ $pegawai->jabatan ?: '-' }}
                            </li>
                        @endforeach
                    </ol>

                </div>

            @endif

            @if($penandatanganDiizinkan === [])

                <div class="alert-error">
                    Tidak ada penandatangan yang diizinkan untuk jabatan pegawai yang ditugaskan.
                    Hubungi administrator.
                </div>

            @else

                <form
                    method="GET"
                    action="{{ $sppd ? route('sppds.pdf', $sppd->id) : route('spts.pdf', $spt->id) }}"
                    target="_blank"
                >

                    <div class="form-group">

                        <label for="penandatangan">Penandatangan</label>

                        <select
                            id="penandatangan"
                            name="penandatangan"
                            required
                        >

                            <option
                                value=""
                                disabled
                                selected
                            >
                                -- Pilih Penandatangan --
                            </option>

                            @foreach($semuaPenandatangan as $kunci => $definisi)

                                @if(in_array($kunci, $penandatanganDiizinkan, true))

                                    <option value="{{ $kunci }}">
                                        {{ $definisi['jabatan'] }}
                                        @if($definisi['nama'])
                                            &mdash; {{ $definisi['nama'] }}
                                        @endif
                                    </option>

                                @endif

                            @endforeach

                        </select>

                    </div>

                    <button type="submit" class="btn-cetak">
                        Cetak PDF
                    </button>

                </form>

            @endif

        </div>

    </div>

@endsection

@push('styles')

<style>

    .card {
        overflow: hidden;
        max-width: 640px;
    }

    .card-header {
        padding: 20px 25px;
        border-bottom: 1px solid #e5e7eb;
    }

    .card-header h2 {
        margin: 0;
        font-size: 18px;
    }

    .card-body {
        padding: 25px;
    }

    .alert-error {
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #991b1b;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 13px;
        margin-bottom: 18px;
    }

    .info-dokumen {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 14px 16px;
        margin-bottom: 20px;
    }

    .info-row {
        display: flex;
        gap: 12px;
        padding: 4px 0;
        font-size: 13px;
    }

    .info-label {
        width: 160px;
        color: #6b7280;
        flex-shrink: 0;
    }

    .info-nilai {
        color: #111827;
        font-weight: 600;
        word-break: break-word;
    }

    .daftar-pegawai {
        margin-bottom: 20px;
    }

    .daftar-pegawai h3 {
        font-size: 14px;
        margin: 0 0 8px;
    }

    .daftar-pegawai ol {
        margin: 0;
        padding-left: 22px;
        font-size: 13px;
        color: #374151;
    }

    .daftar-pegawai li {
        padding: 2px 0;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }

    .form-group select {
        width: 100%;
        padding: 10px 12px;
        font-size: 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #ffffff;
        color: #111827;
    }

    .form-group select:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    .btn-cetak {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #2563eb;
        color: #ffffff;
        border: 1px solid #2563eb;
        font-size: 13px;
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .btn-cetak:hover {
        background: #1d4ed8;
    }

</style>

@endpush
