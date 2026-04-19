<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleCheck
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Cek apakah user sudah login dan role-nya SESUAI dengan yang diminta route
        if (auth()->check() && auth()->user()->role !== $role) {
            abort(403, 'Akses Ditolak! Anda tidak memiliki izin untuk halaman ini.');
        }

        return $next($request);
    }
}