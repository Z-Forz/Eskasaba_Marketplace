<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DashboardRedirectController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        return match (Auth::user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('buyer.dashboard'),
        };
    }
}