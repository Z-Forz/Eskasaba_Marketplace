<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    /**
     * Paksa user sekolah ganti password default sebelum bisa akses fitur.
     *
     * Dikecualikan secara otomatis dari route:
     *   - GET/PUT /password/change
     *   - POST /logout
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('web')->user();

        if ($user && $user->is_default_password) {
            // Izinkan akses ke password change dan logout
            if ($request->routeIs('password.change', 'password.update', 'logout')) {
                return $next($request);
            }

            return redirect()->route('password.change')
                ->with('warning', 'Silakan ganti password default Anda terlebih dahulu.');
        }

        return $next($request);
    }
}
