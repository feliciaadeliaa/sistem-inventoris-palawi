<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['user', 'senior_analis', 'gm'])) {
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}