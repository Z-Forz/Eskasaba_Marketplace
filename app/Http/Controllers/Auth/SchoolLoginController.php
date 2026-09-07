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
        if ($request->hasAny(['nis_nip', 'nis', 'nip', 'user_id', 'id', 'code', 'token', 'access_token', 'username', 'email', 'sso_token', 'ticket'])) {
            return app(SchoolCallbackController::class)->handle($request);
        }

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

        $rawInput = strtolower(trim($credentials['nis_nip']));

        // Cari NIS/NIP dan user lokal baik input berupa NIS/NIP langsung maupun Email Sekolah
        if (str_contains($rawInput, '@')) {
            $localUser = User::where('email', $rawInput)->first();
            $nisNip = $localUser?->nis_nip ?? explode('@', $rawInput)[0];
        } else {
            $nisNip = $rawInput;
            $localUser = User::where('nis_nip', $nisNip)
                ->orWhere('email', $nisNip . '@smkn1bangsri.sch.id')
                ->orWhere('email', $nisNip . '@sijuna.com')
                ->first();
        }

        // Validasi password: password default 'password' ATAU cocok dengan hash password lokal
        $passwordMatches = ($credentials['password'] === 'password')
            || ($localUser && Hash::check($credentials['password'], $localUser->password));

        if (! $passwordMatches) {
            throw ValidationException::withMessages([
                'nis_nip' => 'NIS/NIP atau password salah.',
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
                ?? (str_contains($rawInput, '@') ? $rawInput : ($apiData['nis_nip'] . '@' . $defaultDomain));

            $userData = [
                'username'            => $apiData['nama'],
                'email'               => $userEmail,
                'role'                => $role,
                'class_room'          => $apiData['class_room'] ?? null,
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
                    'nis_nip' => 'NIS/NIP atau Email Sekolah tidak ditemukan di sistem.',
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