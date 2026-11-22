<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Tampilkan halaman registrasi customer.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Daftarkan akun customer baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:25'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        // Ambil bahasa yang sedang dipilih pengunjung.
        $locale = $request->session()->get('locale', 'id');

        if (! in_array($locale, ['id', 'en'], true)) {
            $locale = 'id';
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'preferred_language' => $locale,
        ]);

        event(new Registered($user));

        Auth::login($user);

        $request->session()->regenerate();
        $request->session()->put('locale', $locale);

        return redirect()->route('dashboard');
    }
}
