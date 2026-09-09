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
    public function create(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // Jika request membawa parameter SSO dari Portal SiPintu, proses SSO login secara otomatis
        $ssoParams = ['code', 'token', 'sso_token', 'nis_nip', 'nis', 'nip', 'email', 'username', 'data'];
        foreach ($ssoParams as $param) {
            if ($request->has($param) && ! empty($request->input($param))) {
                return app(\App\Http\Controllers\Auth\SchoolCallbackController::class)->handle($request);
            }
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
        $nisNipInput = trim($credentials['nis_nip']);
        $inputPassword = $credentials['password'];

        $cleanNisNip = str_contains($nisNipInput, '@') ? explode('@', $nisNipInput)[0] : $nisNipInput;

        // 1. Cek kredensial di database lokal terlebih dahulu (match NIS/NIP, email persis, atau email prefix)
        $localUser = User::where(function ($query) use ($nisNipInput, $cleanNisNip) {
            $query->where('nis_nip', $nisNipInput)
                ->orWhere('nis_nip', $cleanNisNip)
                ->orWhere('email', $nisNipInput)
                ->orWhere('email', 'like', $cleanNisNip . '@%');
        })->first();

        if ($localUser && Hash::check($inputPassword, $localUser->password)) {
            Auth::login($localUser);
            $request->session()->regenerate();

            if (class_exists(\App\Models\ActivityLog::class)) {
                \App\Models\ActivityLog::record(
                    $localUser->id,
                    'login',
                    "Login berhasil dari IP {$request->ip()}",
                    $request
                );
            }

            return redirect()->intended(route('dashboard'));
        }

        // 2. Jika password default sekolah ('password') atau API sekolah terhubung, validasi ke API Gateway
        $apiData = $this->schoolApi->validate($nisNipInput) ?? $this->schoolApi->validate($cleanNisNip);

        if ($apiData && ($inputPassword === 'password' || ($localUser && Hash::check($inputPassword, $localUser->password)))) {
            $role = match (strtolower($apiData['jenis_pengguna'] ?? 'siswa')) {
                'guru', 'teacher' => 'teacher',
                default           => 'student',
            };

            $userEmail = $apiData['email'] ?? ($localUser ? $localUser->email : null);
            if (empty($userEmail)) {
                $userEmail = $cleanNisNip . '@sijuna.com';
            }

            // Update atau buat akun lokal secara otomatis
            $localUser = User::updateOrCreate(
                ['nis_nip' => $apiData['nis_nip']],
                [
                    'username'            => $apiData['nama'],
                    'email'               => $userEmail,
                    'role'                => $role,
                    'class_room'          => $apiData['class_room'] ?? null,
                    'phone'               => $apiData['telepon'] ?? null,
                    'api_id'              => $apiData['id'] ?? ($localUser ? $localUser->api_id : rand(1000, 9999)),
                    'password'            => $localUser ? $localUser->password : Hash::make('password'),
                    'is_default_password' => $localUser ? $localUser->is_default_password : true,
                ]
            );

            Auth::login($localUser);
            $request->session()->regenerate();

            if (class_exists(\App\Models\ActivityLog::class)) {
                \App\Models\ActivityLog::record(
                    $localUser->id,
                    'login',
                    "Login berhasil dari IP {$request->ip()}",
                    $request
                );
            }

            return redirect()->intended(route('dashboard'));
        }

        // 3. Fallback jika user tidak ditemukan atau password salah
        throw ValidationException::withMessages([
            'email' => 'NIS/NIP, Email, atau kata sandi tidak sesuai.',
        ]);
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