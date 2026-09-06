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

        $rawInput = strtolower(trim($credentials['nis_nip']));

        // Login wajib menggunakan format email sekolah (contoh: nis@smkn1bangsri.sch.id atau nis@sijuna.com)
        if (! str_contains($rawInput, '@')) {
            throw ValidationException::withMessages([
                'nis_nip' => 'Login wajib menggunakan alamat email sekolah (contoh: nis@smkn1bangsri.sch.id atau nis@sijuna.com).',
            ]);
        }

        // 1. Cari pengguna langsung dari kolom email pada tabel users
        $localUser = User::where('email', $rawInput)->first();
        $nisNip = $localUser?->nis_nip ?? explode('@', $rawInput)[0];

        // 2. Hit API Sekolah untuk validasi & sinkronisasi data pengguna terbaru
        $apiData = $this->schoolApi->validate($nisNip);

        if ($apiData) {
            $role = match (strtolower($apiData['jenis_pengguna'] ?? 'siswa')) {
                'guru', 'teacher' => 'teacher',
                default           => 'student',
            };

            $isJunior = preg_match('/^(X|XI)\s/i', trim((string) ($apiData['class_room'] ?? '')));
            $defaultDomain = $isJunior ? 'sijuna.com' : 'smkn1bangsri.sch.id';

            $userData = [
                'username'            => $apiData['nama'],
                'email'               => $localUser?->email ?? $apiData['email'] ?? $rawInput ?? ($apiData['nis_nip'] . '@' . $defaultDomain),
                'role'                => $role,
                'class_room'          => $apiData['class_room'] ?? null,
                'api_id'              => $apiData['id'] ?? null,
                'password'            => Hash::make('password'),
                'is_default_password' => true,
            ];

            if (!empty($apiData['telepon'])) {
                $userData['phone'] = $apiData['telepon'];
            }

            // Update atau buat akun lokal secara otomatis
            $localUser = User::updateOrCreate(
                ['nis_nip' => $apiData['nis_nip']],
                $userData
            );
        } else {
            // Fallback jika API Sekolah sedang offline / bermasalah, tapi user sudah ada di tabel lokal
            if (! $localUser) {
                $localUser = User::where('nis_nip', $nisNip)->first();
            }

            if (! $localUser) {
                throw ValidationException::withMessages([
                    'nis_nip' => 'Alamat email sekolah tidak ditemukan di sistem.',
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