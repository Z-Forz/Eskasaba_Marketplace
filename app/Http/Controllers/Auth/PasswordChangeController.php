<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PasswordChangeController extends Controller
{
    /**
     * Tampilkan form ganti password untuk user sekolah (siswa/guru).
     * Route ini hanya bisa diakses oleh user dengan guard 'web' (bukan admin).
     */
    public function edit(): View
    {
        return view('auth.password-change');
    }

    /**
     * Proses ganti password.
     * - Jika masih is_default_password: hanya validasi field password baru (bukan current_password)
     *   karena password defaultnya adalah 'password' yang sudah di-hash.
     * - Jika sudah pernah ganti: wajib masukkan current_password.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if ($user->is_default_password) {
            // Ganti password pertama kali: tidak perlu current_password
            $request->validate([
                'password' => ['required', 'confirmed', Password::min(8)],
            ]);
        } else {
            // Ganti password manual: wajib masukkan password lama
            $request->validate([
                'current_password' => ['required', 'current_password'],
                'password'         => ['required', 'confirmed', Password::min(8)],
            ]);
        }

        $user->update([
            'password'            => Hash::make($request->password),
            'is_default_password' => false,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Password berhasil diubah. Selamat datang!');
    }
}