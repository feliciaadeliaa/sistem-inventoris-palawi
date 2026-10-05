<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * Alur: daftar -> verifikasi email -> menunggu persetujuan admin.
     * Akun baru dibuat NONAKTIF (is_active = false) sampai admin mengaktifkan.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'name'  => trim((string) $request->name),
            'email' => strtolower(trim((string) $request->email)),
        ]);

        $emailRule = app()->isProduction() ? 'email:rfc,dns' : 'email';

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', $emailRule, 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password, // di-hash oleh cast 'hashed'
            'role' => 'user',      // role ditetapkan server, bukan dari input
            'is_active' => false,  // menunggu persetujuan admin
        ]);

        event(new Registered($user)); // mengirim email verifikasi

        Auth::login($user);

        return redirect()->route('verification.notice');
    }
}