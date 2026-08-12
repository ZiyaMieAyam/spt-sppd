@extends('layouts.app')

@section('title', 'Beranda - SPT & SPPD')

@section('content')

    <div class="page-header">
        <h1>Beranda</h1>
        <p>Kelola data Surat Perintah Tugas dan Surat Perjalanan Dinas.</p>
    </div>


    <div class="menu-grid">

        <a href="{{ route('dalam-daerah') }}" class="menu-card">

            <div class="menu-icon">
                📍
            </div>

            <div>
                <h2>Dalam Daerah</h2>
                <p>
                    Kelola perjalanan dinas dalam wilayah Kabupaten Balangan.
                </p>
            </div>

        </a>


        <a href="{{ route('luar-daerah') }}" class="menu-card">

            <div class="menu-icon">
                🚌
            </div>

            <div>
                <h2>Luar Daerah</h2>
                <p>
                    Kelola perjalanan dinas ke luar wilayah Kabupaten Balangan.
                </p>
            </div>

        </a>

    </div>

@endsection


@push('styles')

<style>

    .menu-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .menu-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 25px;
        display: flex;
        align-items: center;
        gap: 20px;
        text-decoration: none;
        color: #111827;
        transition: 0.2s;
    }

    .menu-card:hover {
        border-color: #2563eb;
        transform: translateY(-2px);
    }

    .menu-icon {
        width: 55px;
        height: 55px;
        border-radius: 10px;
        background: #eff6ff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 25px;
        flex-shrink: 0;
    }

    .menu-card h2 {
        margin: 0 0 7px;
        font-size: 18px;
    }

    .menu-card p {
        margin: 0;
        color: #6b7280;
        font-size: 13px;
        line-height: 1.5;
    }

    @media (max-width: 700px) {

        .menu-grid {
            grid-template-columns: 1fr;
        }

    }

</style>

@endpush