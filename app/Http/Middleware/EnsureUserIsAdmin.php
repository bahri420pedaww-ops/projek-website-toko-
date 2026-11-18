<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // Kalau belum login, atau login tapi bukan admin: tolak
        if (! $request->user() || ! $request->user()->isAdmin()) {
            abort(403, 'Hanya admin yang boleh mengakses halaman ini.');
        }

        // Kalau admin: silakan lewat
        return $next($request);
    }
}