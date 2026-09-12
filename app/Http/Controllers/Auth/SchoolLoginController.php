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
        $isEmailInput = str_contains($nisNipInput, '@');

        // 1. Cek kredensial di database lokal terlebih dahulu
        // Jika input mengandung '@', HANYA cocokkan persis ke kolom email ($user->email).
        // Jangan pernah memotong domain dan mencocokkan ke NIS/NIP agar 4716@gmail.com tidak bisa masuk ke akun 4716@smkn1bangsri.sch.id.
        $localUser = User::where(function ($query) use ($nisNipInput, $isEmailInput) {
            if ($isEmailInput) {
                $query->where('email', $nisNipInput);
            } else {
                $query->where('nis_nip', $nisNipInput)
                    ->orWhere('username', $nisNipInput);
            }
        })->first();

        // Bagi akun siswa, login WAJIB menggunakan format email sekolah resmi yang terdaftar, tidak boleh hanya NIS saja.
        if ($localUser && $localUser->role === 'student' && !$isEmailInput) {
            throw ValidationException::withMessages([
                'email' => 'Siswa wajib menggunakan Email Sekolah (contoh: NIS@sijuna.com atau NIS@smkn1bangsri.sch.id), bukan NIS saja.',
            ]);
        }

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

        // 2. Jika user lokal belum ada atau password default, validasi ke API Gateway
        $cleanNisNip = $isEmailInput ? explode('@', $nisNipInput)[0] : $nisNipInput;
        $apiData = $this->schoolApi->validate($nisNipInput) ?? ($isEmailInput ? null : $this->schoolApi->validate($cleanNisNip));

        if ($apiData) {
            $role = match (strtolower($apiData['jenis_pengguna'] ?? 'siswa')) {
                'guru', 'teacher' => 'teacher',
                default           => 'student',
            };

            // Bagi akun siswa dari API Gateway, login WAJIB menggunakan format email
            if ($role === 'student' && !$isEmailInput) {
                throw ValidationException::withMessages([
                    'email' => 'Siswa wajib menggunakan Email Sekolah (contoh: NIS@sijuna.com atau NIS@smkn1bangsri.sch.id), bukan NIS saja.',
                ]);
            }

            // Hanya siswa aktif (Kelas 10, 11, dan 12) yang dapat mengakses sistem
            if ($role === 'student') {
                $classRoom = $apiData['class_room'] ?? null;
                if (empty($classRoom) || !preg_match('/^(kelas\s+|kls\s+)?(X|XI|XII|10|11|12)(\s+|-|:|$)/i', trim((string) $classRoom))) {
                    throw ValidationException::withMessages([
                        'email' => 'Hanya siswa aktif (Kelas 10, 11, dan 12) yang dapat mengakses sistem.',
                    ]);
                }
            }

            // Jika memasukkan email, email input HARUS cocok persis dengan email resmi dari API
            if ($isEmailInput && !empty($apiData['email']) && strtolower($apiData['email']) !== strtolower($nisNipInput)) {
                throw ValidationException::withMessages([
                    'email' => 'Email atau kata sandi tidak sesuai.',
                ]);
            }

            if ($inputPassword === 'password' || ($localUser && Hash::check($inputPassword, $localUser->password))) {
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