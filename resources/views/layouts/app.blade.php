<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'SPT & SPPD')</title>

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
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
            font-weight: bold;
            color: #111827;
            text-decoration: none;
        }

        .navbar-brand img {
            width: 38px;
            height: 38px;
            object-fit: contain;
        }

        .navbar-menu {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .navbar-menu a {
            text-decoration: none;
            color: #4b5563;
            font-size: 14px;
        }

        .navbar-menu a:hover {
            color: #2563eb;
        }

        .logout-button {
            border: none;
            background: none;
            color: #dc2626;
            font-size: 14px;
            cursor: pointer;
            padding: 0;
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
                padding: 0 20px;
            }

            .navbar-menu {
                gap: 12px;
            }

            .navbar-menu a {
                font-size: 12px;
            }

            .main {
                padding: 20px;
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

            <span>SPT & SPPD</span>

        </a>


        <div class="navbar-menu">

            <a href="{{ route('beranda') }}">
                Beranda
            </a>

            <a href="{{ route('dalam-daerah') }}">
                Dalam Daerah
            </a>

            <a href="{{ route('luar-daerah') }}">
                Luar Daerah
            </a>

            <a href="{{ route('form') }}">
                Form
            </a>

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