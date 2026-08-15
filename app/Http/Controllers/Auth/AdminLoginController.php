<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminLoginController extends Controller
{
    /**
     * Display admin login page.
     */
    public function create(): View
    {
        return view('auth.admin.login');
    }

    /**
     * Handle admin login request.
     * Menggunakan guard 'admin' (tabel admins, terpisah dari users).
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors([
                    'username' => 'Username atau password salah.',
                ])
                ->onlyInput('username');
        }

        $request->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    /**
     * Logout admin dari guard 'admin'.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()
            ->route('admin.login')
            ->with('status', 'Anda telah berhasil logout.');
    }
}