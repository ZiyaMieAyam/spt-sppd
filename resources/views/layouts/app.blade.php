<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'SiPerjadin')</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f3f4f6;
            color: #111827;
        }

        .navbar {
            height: 65px;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 35px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #111827;
        }

        .navbar-brand img {
            width: 38px;
            height: 38px;
            object-fit: contain;
        }

        .navbar-brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .navbar-brand-name {
            font-size: 17px;
            font-weight: 700;
            color: #111827;
        }

        .navbar-brand-sub {
            font-size: 11px;
            color: #6b7280;
            font-weight: 400;
        }

        .navbar-menu {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .navbar-menu a {
            text-decoration: none;
            color: #4b5563;
            font-size: 14px;
            padding: 8px 14px;
            border-radius: 8px;
            transition: background 0.15s ease, color 0.15s ease;
        }

        .navbar-menu a:hover {
            background: #f3f4f6;
            color: #2563eb;
        }

        .navbar-admin {
            background: #fef3c7;
            color: #92400e;
            font-weight: 600;
        }

        .navbar-admin:hover {
            background: #fde68a;
            color: #92400e;
        }

        .logout-button {
            border: none;
            background: none;
            color: #dc2626;
            font-size: 14px;
            cursor: pointer;
            padding: 8px 14px;
            border-radius: 8px;
            transition: background 0.15s ease;
        }

        .logout-button:hover {
            background: #fef2f2;
        }

        .main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
        }

        .page-header {
            margin-bottom: 25px;
        }

        .page-header h1 {
            margin: 0;
            font-size: 26px;
        }

        .page-header p {
            margin: 7px 0 0;
            color: #6b7280;
            font-size: 14px;
        }

        .card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
        }

        @media (max-width: 700px) {
            .navbar {
                padding: 0 16px;
            }

            .navbar-brand-sub {
                display: none;
            }

            .navbar-menu {
                gap: 2px;
            }

            .navbar-menu a {
                font-size: 12px;
                padding: 6px 8px;
            }

            .navbar-admin {
                font-size: 12px;
                padding: 6px 8px;
            }

            .logout-button {
                font-size: 12px;
                padding: 6px 8px;
            }

            .main {
                padding: 16px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

    <nav class="navbar">

        <a href="{{ route('beranda') }}" class="navbar-brand">

            <img
                src="{{ asset('images/logo-balangan.png') }}"
                alt="Logo Kabupaten Balangan"
            >

            <div class="navbar-brand-text">
                <span class="navbar-brand-name">SiPerjadin</span>
                <span class="navbar-brand-sub">Sistem Perjalanan Dinas</span>
            </div>

        </a>


        <div class="navbar-menu">

            <a href="{{ route('beranda') }}">
                Beranda
            </a>

            <a href="{{ route('form') }}">
                Form
            </a>

            <a href="{{ route('dalam-daerah') }}">
                Dalam Daerah
            </a>

            <a href="{{ route('luar-daerah') }}">
                Luar Daerah
            </a>

            @if(auth()->user()->isAdmin())

            <a href="{{ url('/admin') }}" target="_blank" class="navbar-admin">
                Admin Panel
            </a>

            @endif

            <form action="{{ route('logout') }}" method="POST">
                @csrf

                <button type="submit" class="logout-button">
                    Logout
                </button>
            </form>

        </div>

    </nav>


    <main class="main">

        @yield('content')

    </main>

    @stack('scripts')

</body>

</html>
