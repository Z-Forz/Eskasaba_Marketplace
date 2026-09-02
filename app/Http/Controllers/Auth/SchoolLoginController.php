<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Services\SchoolApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SchoolLoginController extends Controller
{
    public function __construct(private SchoolApiService $schoolApi) {}

    /**
     * Display login page.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     * Login menggunakan NIS/NIP dan password default dari API Sekolah ('password').
     * Data akun otomatis disinkronkan dari API Sekolah.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated(); // ['nis_nip' => ..., 'password' => ...]

        // Password default sistem sekolah = 'password'
        if ($credentials['password'] !== 'password') {
            throw ValidationException::withMessages([
                'nis_nip' => 'NIS/NIP atau password salah.',
            ]);
        }

        // Hit API Sekolah untuk validasi NIS/NIP & ambil data pengguna terbaru
        $apiData = $this->schoolApi->validate($credentials['nis_nip']);

        if ($apiData) {
            $role = match (strtolower($apiData['jenis_pengguna'] ?? 'siswa')) {
                'guru', 'teacher' => 'teacher',
                default           => 'student',
            };

            // Update atau buat akun lokal secara otomatis
            $localUser = User::updateOrCreate(
                ['nis_nip' => $apiData['nis_nip']],
                [
                    'username'            => $apiData['nama'],
                    'email'               => $apiData['email'] ?? ($apiData['nis_nip'] . '@sekolah.id'),
                    'role'                => $role,
                    'class_room'          => $apiData['class_room'] ?? null,
                    'phone'               => $apiData['telepon'] ?? null,
                    'api_id'              => $apiData['id'] ?? null,
                    'password'            => Hash::make('password'),
                    'is_default_password' => true,
                ]
            );
        } else {
            // Fallback jika API Sekolah sedang offline / bermasalah, tapi user sudah ada lokal
            $localUser = User::where('nis_nip', $credentials['nis_nip'])->first();

            if (! $localUser) {
                throw ValidationException::withMessages([
                    'nis_nip' => 'NIS/NIP tidak ditemukan di sistem sekolah.',
                ]);
            }

            // Pastikan password lokal disesuaikan dengan password default
            if (! Hash::check('password', $localUser->password)) {
                $localUser->update([
                    'password'            => Hash::make('password'),
                    'is_default_password' => true,
                ]);
            }
        }

        Auth::login($localUser);

        $request->session()->regenerate();

        // Catat log login
        \App\Models\ActivityLog::record(
            $localUser->id,
            'login',
            "Login berhasil dari IP {$request->ip()}",
            $request
        );

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}