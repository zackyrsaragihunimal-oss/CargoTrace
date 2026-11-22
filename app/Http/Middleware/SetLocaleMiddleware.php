<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Mengatur bahasa aplikasi berdasarkan preferensi pengguna.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLanguages = ['id', 'en'];

        // Bahasa default untuk pengunjung.
        $locale = $request->session()->get('locale', 'id');

        // Preferensi akun menjadi prioritas ketika pengguna login.
        $user = $request->user();

        if ($user) {
            $preferredLanguage = $user->preferred_language;

            $locale = in_array($preferredLanguage, $supportedLanguages, true)
                ? $preferredLanguage
                : 'id';
        }

        // Pastikan hanya bahasa yang didukung yang digunakan.
        if (! in_array($locale, $supportedLanguages, true)) {
            $locale = 'id';
        }

        App::setLocale($locale);

        return $next($request);
    }
}
