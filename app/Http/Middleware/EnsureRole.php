<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * Penggunaan di routes:
     *   ->middleware('role:admin')  — cek guard 'admin' (tabel admins)
     *   ->middleware('role:user')   — cek guard 'web'   (tabel users)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        foreach ($roles as $role) {
            // 'admin' → cek guard admin (tabel admins)
            if ($role === 'admin' && Auth::guard('admin')->check()) {
                return $next($request);
            }

            // 'user' / 'student' / 'teacher' → cek guard web (tabel users)
            if (in_array($role, ['user', 'student', 'teacher']) && Auth::guard('web')->check()) {
                return $next($request);
            }
        }

        // Belum login atau tidak punya akses
        if (in_array('admin', $roles)) {
            return redirect()->route('admin.login')
                ->withErrors(['auth' => 'Silakan login terlebih dahulu.']);
        }

        return redirect()->route('login')
            ->withErrors(['auth' => 'Silakan login terlebih dahulu.']);
    }
}
