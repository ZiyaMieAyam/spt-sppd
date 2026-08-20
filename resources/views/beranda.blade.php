@extends('layouts.app')

@section('title', 'Beranda - SiPerjadin')

@section('content')

    <div class="hero">
        <h1>Selamat Datang di SiPerjadin</h1>
        <p>Sistem Pengelolaan Surat Perintah Tugas dan Surat Perintah Perjalanan Dinas<br>Dinas Komunikasi Informatika, Statistik dan Persandian Kabupaten Balangan</p>
    </div>


    <div class="menu-grid">

        <a href="{{ route('dalam-daerah') }}" class="menu-card">

            <div class="menu-icon menu-icon--blue">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
            </div>

            <div class="menu-content">
                <h2>Dalam Daerah</h2>
                <p>
                    Kelola perjalanan dinas dalam wilayah Kabupaten Balangan.
                </p>
            </div>

            <svg class="menu-arrow" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m9 18 6-6-6-6"/>
            </svg>

        </a>


        <a href="{{ route('luar-daerah') }}" class="menu-card">

            <div class="menu-icon menu-icon--amber">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                </svg>
            </div>

            <div class="menu-content">
                <h2>Luar Daerah</h2>
                <p>
                    Kelola perjalanan dinas ke luar wilayah Kabupaten Balangan.
                </p>
            </div>

            <svg class="menu-arrow" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m9 18 6-6-6-6"/>
            </svg>

        </a>


        <a href="{{ route('form') }}" class="menu-card">

            <div class="menu-icon menu-icon--violet">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"/>
                </svg>
            </div>

            <div class="menu-content">
                <h2>Form SPT & SPPD</h2>
                <p>
                    Buat atau ubah data Surat Perintah Tugas dan SPPD.
                </p>
            </div>

            <svg class="menu-arrow" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m9 18 6-6-6-6"/>
            </svg>

        </a>

    </div>

@endsection


@push('styles')

<style>

    .hero {
        margin-bottom: 32px;
    }

    .hero h1 {
        margin: 0 0 8px;
        font-size: 28px;
        font-weight: 700;
        color: #111827;
    }

    .hero p {
        margin: 0;
        color: #6b7280;
        font-size: 14px;
        line-height: 1.6;
    }

    .menu-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    .menu-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 18px;
        text-decoration: none;
        color: #111827;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .menu-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }

    .menu-card:hover .menu-arrow {
        opacity: 1;
        transform: translateX(0);
    }

    .menu-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: transform 0.2s ease;
    }

    .menu-card:hover .menu-icon {
        transform: scale(1.05);
    }

    .menu-icon--blue {
        background: #eff6ff;
        color: #2563eb;
    }

    .menu-icon--amber {
        background: #fffbeb;
        color: #d97706;
    }

    .menu-icon--violet {
        background: #f5f3ff;
        color: #7c3aed;
    }

    .menu-content {
        flex: 1;
        min-width: 0;
    }

    .menu-content h2 {
        margin: 0 0 5px;
        font-size: 17px;
        font-weight: 600;
    }

    .menu-content p {
        margin: 0;
        color: #6b7280;
        font-size: 13px;
        line-height: 1.5;
    }

    .menu-arrow {
        flex-shrink: 0;
        color: #9ca3af;
        opacity: 0;
        transform: translateX(-4px);
        transition: opacity 0.2s ease, transform 0.2s ease;
    }

    @media (max-width: 700px) {

        .hero h1 {
            font-size: 22px;
        }

        .menu-grid {
            grid-template-columns: 1fr;
        }

        .menu-card {
            padding: 20px;
        }

        .menu-arrow {
            display: none;
        }

    }

</style>

@endpush
