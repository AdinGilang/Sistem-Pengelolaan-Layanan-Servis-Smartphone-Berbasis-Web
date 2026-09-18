<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            // Rules\Password::defaults() diatur terpusat di AppServiceProvider:
            // minimal 10 karakter, kombinasi huruf besar-kecil, angka, simbol,
            // dan di production ditolak bila pernah muncul di kebocoran data.
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = new User();
        $user->name     = $validated['name'];
        $user->email    = $validated['email'];
        $user->password = $validated['password']; // di-hash otomatis oleh cast.

        // Peran ditetapkan dari konfigurasi server, bukan dari input request.
        // Kolom "role" juga sudah dikeluarkan dari $fillable sehingga tidak
        // bisa diselipkan lewat body request.
        $user->role = config('auth.registration_role', User::ROLE_ADMIN);

        $user->save();

        event(new Registered($user));

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
