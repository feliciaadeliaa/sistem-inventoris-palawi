<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Tolak user yang akunnya dinonaktifkan, termasuk yang sesinya
     * sudah terlanjur login sebelum dinonaktifkan admin.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->is_active) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Akun Anda belum aktif. Jika baru mendaftar, tunggu persetujuan admin. Jika sebelumnya aktif, hubungi admin.']);
        }

        return $next($request);
    }
}