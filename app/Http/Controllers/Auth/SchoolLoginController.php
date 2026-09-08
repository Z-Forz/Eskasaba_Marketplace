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
        $nisNip = trim($credentials['nis_nip']);
        $inputPassword = $credentials['password'];

        // 1. Cek kredensial di database lokal terlebih dahulu (apakah password cocok dengan hash lokal)
        $localUser = User::where('nis_nip', $nisNip)
            ->orWhere('email', $nisNip)
            ->orWhere('email', 'like', $nisNip . '@%')
            ->first();

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
        $apiData = $this->schoolApi->validate($nisNip);

        if ($apiData && ($inputPassword === 'password' || ($localUser && Hash::check($inputPassword, $localUser->password)))) {
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
            'nis_nip' => 'NIS/NIP atau kata sandi tidak sesuai.',
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