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
        return match (Auth::user()->role) {
            'teacher' => redirect()->route('seller.dashboard'),
            default   => redirect()->route('buyer.dashboard'),
        };
    }
}