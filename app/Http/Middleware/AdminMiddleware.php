<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('admin.login')
                ->with('error', 'Silakan masuk terlebih dahulu untuk mengakses Panel Admin Cakrawala.');
        }

        if (! auth()->user()->isAdmin()) {
            auth()->logout();

            return redirect()->route('admin.login')
                ->with('error', 'Akses ditolak. Akun Anda bukan administrator.');
        }

        return $next($request);
    }
}
