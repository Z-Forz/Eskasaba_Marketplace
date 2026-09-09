<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DashboardRedirectController extends Controller
{
    /**
     * Redirect ke dashboard sesuai role pengguna sekolah (siswa/guru).
     * Admin tidak menggunakan controller ini — admin punya guard sendiri ('admin').
     */
    public function __invoke(): RedirectResponse
    {
        $user = Auth::user();

        if ($user?->seller && $user->seller->isApproved()) {
            return redirect()->route('seller.dashboard');
        }

        return redirect()->route('buyer.dashboard');
    }
}