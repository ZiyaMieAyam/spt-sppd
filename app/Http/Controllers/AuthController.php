<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $key = Str::transliterate(
            Str::lower($request->input('email')) . '|' . $request->ip()
        );

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return back()
                ->withErrors([
                    'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
                ])
                ->withInput($request->only('email'));
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($key);

            $request->session()->regenerate();

            return redirect()->route('beranda');
        }

        RateLimiter::hit($key, 60);

        return back()
            ->withErrors([
                'email' => 'Email atau password salah.',
            ])
            ->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}