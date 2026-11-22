<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::lower($request->input('email')).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Terlalu banyak percobaan login. Silakan coba lagi beberapa saat.',
            ]);
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'email' => 'Email atau password tidak sesuai.',
            ]);
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            RateLimiter::clear($throttleKey);

            throw ValidationException::withMessages([
                'email' => 'Akun Anda sedang dinonaktifkan. Hubungi administrator.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        // Gunakan bahasa yang tersimpan pada akun.
        $locale = in_array($user->preferred_language, ['id', 'en'], true)
            ? $user->preferred_language
            : 'id';

        $request->session()->regenerate();
        $request->session()->put('locale', $locale);

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        // Pertahankan bahasa terakhir sebelum session direset.
        $locale = $request->session()->get('locale', 'id');

        if (! in_array($locale, ['id', 'en'], true)) {
            $locale = 'id';
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Pulihkan pilihan bahasa untuk halaman login.
        $request->session()->put('locale', $locale);

        return redirect()->route('login');
    }
}
