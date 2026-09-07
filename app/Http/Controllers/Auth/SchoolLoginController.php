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
    public function create(Request $request): View|\Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
    {
        if ($request->hasAny(['code', 'nis_nip', 'nis', 'nip', 'user_id', 'id', 'token', 'access_token', 'username', 'sso_token', 'ticket'])) {
            return app(\App\Http\Controllers\OAuthController::class)->callback($request);
        }

        return view('auth.login');
    }

    /**
     * Handle login request.
     * Login menggunakan Email Sekolah dan password default dari API Sekolah ('password').
     * Data akun otomatis disinkronkan dari API Sekolah.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        $rawInput = strtolower(trim($credentials['email'] ?? $credentials['nis_nip'] ?? ''));

        // Login wajib menggunakan format email sekolah (contoh: nis@smkn1bangsri.sch.id atau nis@sijuna.com)
        if (! str_contains($rawInput, '@')) {
            throw ValidationException::withMessages([
                'email' => 'Login wajib menggunakan alamat email sekolah (contoh: nis@smkn1bangsri.sch.id atau nis@sijuna.com).',
            ]);
        }

        $localUser = User::where('email', $rawInput)->first();
        $possibleNis = explode('@', $rawInput)[0];
        if (! $localUser) {
            $localUser = User::where('nis_nip', $possibleNis)->first();
        }
        $nisNip = $localUser?->nis_nip ?? $possibleNis;

        // Validasi password: password default 'password' ATAU cocok dengan hash password lokal
        $passwordMatches = ($credentials['password'] === 'password')
            || ($localUser && Hash::check($credentials['password'], $localUser->password));

        if (! $passwordMatches) {
            throw ValidationException::withMessages([
                'email' => 'Gagal Masuk: Kata sandi yang Anda masukkan salah.',
            ]);
        }

        // 1. Hit API Sekolah untuk validasi & sinkronisasi data pengguna terbaru
        $apiData = $this->schoolApi->validate($nisNip);

        if ($apiData) {
            $role = match (strtolower($apiData['jenis_pengguna'] ?? 'siswa')) {
                'guru', 'teacher' => 'teacher',
                default           => 'student',
            };

            $isJunior = preg_match('/^(X|XI)\s/i', trim((string) ($apiData['class_room'] ?? '')));
            $defaultDomain = $isJunior ? 'sijuna.com' : 'smkn1bangsri.sch.id';

            $userEmail = $localUser?->email
                ?? $apiData['email']
                ?? $rawInput;

            $classRoom = $apiData['class_room'] ?? null;
            if ($role === 'teacher' && empty($classRoom)) {
                $classRoom = 'Dewan Guru';
            }

            $userData = [
                'username'            => $apiData['nama'],
                'email'               => $userEmail,
                'role'                => $role,
                'class_room'          => $classRoom,
                'api_id'              => $apiData['id'] ?? null,
            ];

            if (! empty($apiData['telepon'])) {
                $userData['phone'] = $apiData['telepon'];
            }

            if (! $localUser) {
                $userData['password'] = Hash::make($credentials['password']);
                $userData['is_default_password'] = ($credentials['password'] === 'password');
            }

            // Update atau buat akun lokal secara otomatis
            $localUser = User::updateOrCreate(
                ['nis_nip' => $apiData['nis_nip']],
                $userData
            );
        } else {
            // Fallback jika API Sekolah sedang offline / bermasalah, tapi user sudah ada di tabel lokal
            if (! $localUser) {
                throw ValidationException::withMessages([
                    'email' => "Gagal Masuk: Alamat email '{$rawInput}' tidak terdaftar di sistem sekolah.",
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