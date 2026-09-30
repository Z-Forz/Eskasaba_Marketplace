<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminLoginController extends Controller
{
    /**
     * Display admin login page.
     */
    public function create(): View|RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

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

        $admin = Auth::guard('admin')->user();
        ActivityLog::record(
            userId: null,
            event: 'admin_login',
            description: 'Admin '.$admin->name.' ('.$admin->username.') berhasil login.',
            request: $request,
            adminId: $admin->id
        );

        return redirect()->route('admin.dashboard');
    }

    /**
     * Logout admin dari guard 'admin'.
     */
    public function destroy(Request $request): RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            $admin = Auth::guard('admin')->user();
            ActivityLog::record(
                userId: null,
                event: 'admin_logout',
                description: 'Admin '.$admin->name.' ('.$admin->username.') logout dari sistem.',
                request: $request,
                adminId: $admin->id
            );
        }

        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()
            ->route('admin.login')
            ->with('status', 'Anda telah berhasil logout.');
    }
}
