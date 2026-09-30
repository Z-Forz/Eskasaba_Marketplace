<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsolateSession
{
    /**
     * Handle an incoming request.
     *
     * Mengisolasi nama cookie session antara area Admin (/admin) dan area User umum (/...):
     * - Route /admin/* menggunakan session cookie khusus admin (misal: eskasaba_admin_session).
     * - Route user umum (buyer, seller, profile, dll) menggunakan session cookie user (misal: eskasaba_user_session).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->is('admin') || $request->is('admin/*')) {
            config(['session.cookie' => config('session.admin_cookie', 'eskasaba_admin_session')]);
        } else {
            config(['session.cookie' => config('session.cookie', 'eskasaba_user_session')]);
        }

        return $next($request);
    }
}
