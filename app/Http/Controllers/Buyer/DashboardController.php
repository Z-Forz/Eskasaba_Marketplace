<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    /**
     * Dashboard buyer disatukan dengan halaman profil terpadu (/profile).
     */
    public function index(): RedirectResponse
    {
        return redirect()->route('profile.index');
    }
}
