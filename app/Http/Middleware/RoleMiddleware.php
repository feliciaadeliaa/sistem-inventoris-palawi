<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Izinkan akses hanya untuk role yang disebut di route.
     * Contoh pemakaian: ->middleware('role:admin,finance')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check() || !in_array(auth()->user()->role, $roles, true)) {
            abort(403, 'Akses ditolak. Halaman ini khusus ' . implode(' / ', array_map('ucfirst', $roles)) . '.');
        }

        return $next($request);
    }
}