@extends('layouts.app')

@section('title', 'Beranda - SIPERJADIN')

@section('content')

    <div class="hero">

        <div class="hero-logo">
            <img src="{{ asset('images/logo-balangan.png') }}" alt="Logo Kabupaten Balangan">
        </div>

        <h1>SIPERJADIN</h1>
        <p class="hero-sub">Sistem Informasi Perjalanan Dinas</p>
        <p class="hero-desc">
            Aplikasi untuk mengelola data perjalanan dinas, Surat Perintah Tugas (SPT),
            dan Surat Perintah Perjalanan Dinas (SPPD) pada
            Dinas Komunikasi, Informatika, Statistik dan Persandian Kabupaten Balangan.
        </p>

        <a href="{{ route('form') }}" class="btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 5v14"/><path d="M5 12h14"/>
            </svg>
            Buat Perjalanan Dinas
        </a>

    </div>

    <div class="stats-grid">

        <div class="stat-card">

            <div class="stat-icon stat-icon--blue">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                    <path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                </svg>
            </div>

            <div class="stat-content">
                <p class="stat-label">Total SPT</p>
                <p class="stat-value">{{ number_format($totalSpt) }}</p>
            </div>

        </div>

        <div class="stat-card">

            <div class="stat-icon stat-icon--indigo">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                    <path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                    <path d="M8 13h8"/><path d="M8 17h5"/>
                </svg>
            </div>

            <div class="stat-content">
                <p class="stat-label">Total SPPD</p>
                <p class="stat-value">{{ number_format($totalSppd) }}</p>
            </div>

        </div>

        <div class="stat-card">

            <div class="stat-icon stat-icon--sky">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
            </div>

            <div class="stat-content">
                <p class="stat-label">Dalam Daerah</p>
                <p class="stat-value">{{ number_format($dalamDaerah) }}</p>
            </div>

        </div>

        <div class="stat-card">

            <div class="stat-icon stat-icon--cyan">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/>
                    <path d="M2 12h20"/>
                </svg>
            </div>

            <div class="stat-content">
                <p class="stat-label">Luar Daerah</p>
                <p class="stat-value">{{ number_format($luarDaerah) }}</p>
            </div>

        </div>

    </div>

    <div class="section-head">
        <div>
            <h2>Perjalanan Terbaru</h2>
            <p>Data perjalanan dinas terakhir yang tercatat di sistem.</p>
        </div>
    </div>

    <div class="card">

        <div class="table-wrapper">

            <table>

                <thead>
                    <tr>
                        <th>Nomor SPT</th>
                        <th>Nama Pegawai</th>
                        <th>Jenis Perjalanan</th>
                        <th>Tujuan</th>
                        <th>Tanggal Berangkat</th>
                        <th>Tanggal Kembali</th>
                        <th class="col-aksi">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($terbaru as $spt)

                        @php
                            $namaPegawai = $spt->pegawais->pluck('nama')->filter();
                            $pegawaiTampil = $namaPegawai->take(2)->implode(', ');
                            $sisa = max($namaPegawai->count() - 2, 0);
                        @endphp

                        <tr>

                            <td>{{ $spt->nomor_spt }}</td>

                            <td>
                                {{ $pegawaiTampil ?: '-' }}
                                @if ($sisa > 0)
                                    <span class="badge">+{{ $sisa }} lainnya</span>
                                @endif
                            </td>

                            <td>
                                <span class="tag {{ $spt->jenis_perjalanan === 'Dalam Daerah' ? 'tag--dalam' : 'tag--luar' }}">
                                    {{ $spt->jenis_perjalanan }}
                                </span>
                            </td>

                            <td>
                                @if ($spt->jenis_perjalanan === 'Dalam Daerah')
                                    {{ $spt->kecamatan?->nama ?? '-' }}
                                    @if ($spt->desa)
                                        <br>
                                        <small>{{ $spt->desa }}</small>
                                    @endif
                                @else
                                    {{ $spt->kotaTujuan?->nama ?? '-' }}
                                @endif
                            </td>

                            <td>{{ $spt->tanggal_berangkat?->translatedFormat('d M Y') ?? '-' }}</td>

                            <td>{{ $spt->tanggal_kembali?->translatedFormat('d M Y') ?? '-' }}</td>

                            <td class="col-aksi">
                                <a href="{{ route('spts.pdf.pilih', $spt->id) }}" target="_blank" class="btn-print" title="Cetak PDF SPT">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 9V2h12v7"/>
                                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                                        <rect x="6" y="14" width="12" height="8"/>
                                    </svg>
                                    Cetak PDF
                                </a>
                                @if (auth()->user()?->isAdmin())
                                    <form action="{{ route('form.delete', $spt->id) }}" method="POST" style="display:inline;margin-left:8px" onsubmit="return confirm('Yakin ingin menghapus SPT ini beserta seluruh SPPD yang terkait?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete" title="Hapus SPT beserta seluruh SPPD">
                                            Hapus SPT
                                        </button>
                                    </form>
                                @endif
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="empty">
                                Belum ada data perjalanan dinas.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

    <div class="quick-head">
        <h2>Aksi Cepat</h2>
    </div>

    <div class="quick-grid">

        <a href="{{ route('form') }}" class="quick-card">

            <div class="quick-icon quick-icon--blue">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9"/>
                    <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                </svg>
            </div>

            <div class="quick-content">
                <h3>Buat Perjalanan Dinas</h3>
                <p>Input data SPT dan SPPD baru.</p>
            </div>

        </a>

        <a href="{{ route('dalam-daerah') }}" class="quick-card">

            <div class="quick-icon quick-icon--sky">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
            </div>

            <div class="quick-content">
                <h3>Dalam Daerah</h3>
                <p>Lihat data perjalanan dalam wilayah Kab. Balangan.</p>
            </div>

        </a>

        <a href="{{ route('luar-daerah') }}" class="quick-card">

            <div class="quick-icon quick-icon--cyan">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/>
                    <path d="M2 12h20"/>
                </svg>
            </div>

            <div class="quick-content">
                <h3>Luar Daerah</h3>
                <p>Lihat data perjalanan ke luar wilayah Kab. Balangan.</p>
            </div>

        </a>

    </div>

    <div class="card alur">

        <div class="alur-head">
            <h2>Alur Perjalanan Dinas</h2>
        </div>

        <ol class="alur-list">

            <li>
                <span class="alur-no">1</span>
                <span>Input data perjalanan dinas melalui form.</span>
            </li>

            <li>
                <span class="alur-no">2</span>
                <span>Sistem membuat nomor SPT dan SPPD secara otomatis.</span>
            </li>

            <li>
                <span class="alur-no">3</span>
                <span>Data tersimpan pada sistem.</span>
            </li>

            <li>
                <span class="alur-no">4</span>
                <span>SPT dapat dicetak.</span>
            </li>

        </ol>

    </div>

@endsection

@push('styles')
    <style>
        .hero {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            margin-bottom: 26px;
        }

        .hero-logo img {
            width: 64px;
            height: 64px;
            object-fit: contain;
            margin-bottom: 14px;
        }

        .hero h1 {
            margin: 0 0 4px;
            font-size: 30px;
            font-weight: 700;
            color: #111827;
            letter-spacing: 1px;
        }

        .hero-sub {
            margin: 0 0 14px;
            font-size: 15px;
            font-weight: 600;
            color: #2563eb;
        }

        .hero-desc {
            max-width: 620px;
            margin: 0 auto 24px;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.6;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #2563eb;
            color: #ffffff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            padding: 12px 22px;
            border-radius: 8px;
            border: 1px solid #2563eb;
            transition: background 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25);
            transform: translateY(-1px);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.07);
            border-color: #cbd5e1;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon--blue {
            background: #eff6ff;
            color: #2563eb;
        }

        .stat-icon--indigo {
            background: #eef2ff;
            color: #4f46e5;
        }

        .stat-icon--sky {
            background: #f0f9ff;
            color: #0284c7;
        }

        .stat-icon--cyan {
            background: #ecfeff;
            color: #0891b2;
        }

        .stat-label {
            margin: 0 0 3px;
            font-size: 12px;
            color: #6b7280;
        }

        .stat-value {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            color: #111827;
        }

        .section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }

        .section-head h2 {
            margin: 0 0 3px;
            font-size: 19px;
        }

        .section-head p {
            margin: 0;
            color: #6b7280;
            font-size: 13px;
        }

        .card {
            overflow: hidden;
            margin-bottom: 32px;
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

        tbody tr {
            transition: background 0.15s ease;
        }

        tbody tr:hover {
            background: #f9fafb;
        }

        td small {
            color: #6b7280;
        }

        .badge {
            display: inline-block;
            margin-left: 6px;
            background: #eff6ff;
            color: #2563eb;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 999px;
        }

        .tag {
            display: inline-block;
            font-size: 11px;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 999px;
        }

        .tag--dalam {
            background: #dcfce7;
            color: #15803d;
        }

        .tag--luar {
            background: #ffedd5;
            color: #c2410c;
        }

        .empty {
            text-align: center;
            color: #9ca3af;
            padding: 35px;
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

        .quick-head {
            margin-bottom: 14px;
        }

        .quick-head h2 {
            margin: 0;
            font-size: 19px;
        }

        .quick-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 32px;
        }

        .quick-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: #111827;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .quick-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.07);
            border-color: #cbd5e1;
        }

        .quick-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: transform 0.2s ease;
        }

        .quick-card:hover .quick-icon {
            transform: scale(1.06);
        }

        .quick-icon--blue {
            background: #eff6ff;
            color: #2563eb;
        }

        .quick-icon--sky {
            background: #f0f9ff;
            color: #0284c7;
        }

        .quick-icon--cyan {
            background: #ecfeff;
            color: #0891b2;
        }

        .quick-content {
            min-width: 0;
        }

        .quick-content h3 {
            margin: 0 0 3px;
            font-size: 15px;
            font-weight: 600;
        }

        .quick-content p {
            margin: 0;
            color: #6b7280;
            font-size: 13px;
            line-height: 1.4;
        }

        .alur {
            padding: 22px 26px;
        }

        .alur-head {
            padding-bottom: 16px;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 16px;
        }

        .alur-head h2 {
            margin: 0;
            font-size: 17px;
        }

        .alur-list {
            list-style: none;
            margin: 0;
            padding: 0;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 14px 28px;
        }

        .alur-list li {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #374151;
            font-size: 14px;
        }

        .alur-no {
            width: 26px;
            height: 26px;
            flex-shrink: 0;
            border-radius: 999px;
            background: #eff6ff;
            color: #2563eb;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @media (max-width: 900px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 700px) {
            .hero {
                padding: 28px 18px;
            }

            .hero h1 {
                font-size: 24px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .quick-grid {
                grid-template-columns: 1fr;
            }

            .section-head {
                align-items: flex-start;
                flex-direction: column;
            }

            .alur-list {
                grid-template-columns: 1fr;
            }

            .alur {
                padding: 18px;
            }
        }
    </style>
@endpush