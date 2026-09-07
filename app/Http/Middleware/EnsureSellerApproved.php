<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSellerApproved
{
    /**
     * Pastikan user sudah menjadi seller yang disetujui admin
     * sebelum bisa mengakses fitur seller panel.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user   = Auth::guard('web')->user();
        $seller = $user?->seller;

        if (! $seller || ! $seller->isApproved()) {
            return redirect()->route('profile.index')
                ->with('warning', 'Anda belum terdaftar sebagai seller aktif. Silakan ajukan pendaftaran terlebih dahulu.');
        }

        return $next($request);
    }
}
