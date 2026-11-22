<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class LanguageController extends Controller
{
    /**
     * Simpan pilihan bahasa pengguna.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'locale' => ['required', 'in:id,en'],
        ]);

        $locale = $validated['locale'];

        // Simpan bahasa ke akun jika pengguna sudah login.
        if ($request->user()) {
            $request->user()->update([
                'preferred_language' => $locale,
            ]);
        }

        // Simpan juga ke session agar pilihan tersedia setelah logout.
        $request->session()->put('locale', $locale);

        return redirect()->back();
    }
}
