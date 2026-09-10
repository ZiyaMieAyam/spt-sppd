<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SiPerjadin</title>

    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; background: #f3f4f6; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-container { width: 100%; max-width: 420px; padding: 20px; }
        .login-card { background: #ffffff; border-radius: 12px; padding: 35px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08); }
        .logo { text-align: center; margin-bottom: 20px; }
        .logo img { width: 90px; height: 90px; object-fit: contain; }
        .title { text-align: center; margin-bottom: 30px; }
        .title h1 { margin: 0; font-size: 24px; color: #111827; }
        .title p { margin-top: 8px; margin-bottom: 0; color: #6b7280; font-size: 14px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 7px; font-size: 14px; font-weight: bold; color: #374151; }
        .form-group input { width: 100%; padding: 12px 14px; border: 1px solid #d1d5db; border-radius: 7px; font-size: 14px; outline: none; }
        .form-group input:focus { border-color: #2563eb; }
        .error { margin-top: 6px; color: #dc2626; font-size: 13px; }
        .login-button { width: 100%; border: none; border-radius: 7px; padding: 13px; background: #2563eb; color: white; font-size: 15px; font-weight: bold; cursor: pointer; }
        .login-button:hover { background: #1d4ed8; }
        .footer { text-align: center; margin-top: 25px; font-size: 12px; color: #9ca3af; }
    </style>
</head>

<body>

<div class="login-container">
    <div class="login-card">
        <div class="logo">
            <img src="{{ asset('images/logo-balangan.png') }}" alt="Logo Kabupaten Balangan">
        </div>

        <div class="title">
            <h1>SiPerjadin</h1>
            <p>Sistem Perjalanan Dinas</p>
        </div>

        @if(session('error'))
            <div class="error" style="margin-bottom: 15px;">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email">Email</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Masukkan email"
                    required
                    autofocus
                >

                @error('email')
                    <div class="error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Masukkan password"
                    required
                >

                @error('password')
                    <div class="error">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" class="login-button">
                Login
            </button>
        </form>

        <div class="footer">
            Dinas Komunikasi Informatika, Statistik dan Persandian
            <br>
            Kabupaten Balangan
        </div>
    </div>
</div>

</body>

</html>