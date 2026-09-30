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
                'email' => 'Siswa wajib menggunakan Email Sekolah ( email atau nis yang digunakan saat login sijuna, contoh: 1234@smkn1bangsri.sch id / 1234@sijuna.com ).',
            ]);
        }

        // Tolak jika akun siswa lokal berstatus alumni
        if ($localUser && $localUser->role === 'student' && SchoolApiService::isAlumni($localUser->toArray())) {
            $localUser->delete();
            throw ValidationException::withMessages([
                'email' => 'Akun Anda telah berstatus Alumni / Akun Anda tidak terdaftar. Pengaksesan Eskasaba Marketplace hanya diperuntukkan bagi siswa/guru aktif.',
            ]);
        }

        if ($localUser) {
            // Jika user lokal sudah pernah mengganti password bawaan (is_default_password == false)
            if (!$localUser->is_default_password) {
                if (Hash::check($inputPassword, $localUser->password)) {
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

                    return redirect()->route('profile.index')->with('success', 'Berhasil login! Selamat datang kembali, ' . $localUser->username . '.');
                } else {
                    // Password salah! Tolak login & jangan pernah izinkan fallback ke password default 'password'.
                    throw ValidationException::withMessages([
                        'email' => 'NIS/NIP, Email, atau kata sandi tidak sesuai.',
                    ]);
                }
            } else {
                // User lokal masih berstatus password default (is_default_password == true)
                if (Hash::check($inputPassword, $localUser->password) || $inputPassword === 'password') {
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

                    return redirect()->route('profile.index')->with('success', 'Berhasil login! Selamat datang kembali, ' . $localUser->username . '.');
                } else {
                    // Pengguna memasukkan password baru buatan mereka (bukan 'password' default)
                    // Simpan password baru ini ke database lokal dan ubah is_default_password menjadi false
                    $localUser->update([
                        'password'            => Hash::make($inputPassword),
                        'is_default_password' => false,
                    ]);

                    Auth::login($localUser);
                    $request->session()->regenerate();

                    if (class_exists(\App\Models\ActivityLog::class)) {
                        \App\Models\ActivityLog::record(
                            $localUser->id,
                            'login',
                            "Login & aktivasi password baru berhasil dari IP {$request->ip()}",
                            $request
                        );
                    }

                    return redirect()->route('profile.index')->with('success', 'Berhasil login dengan kata sandi baru! Selamat datang kembali, ' . $localUser->username . '.');
                }
            }
        }

        // 2. Jika user lokal belum ada, divalidasi ke API Gateway SiPintu
        $cleanNisNip = $isEmailInput ? explode('@', $nisNipInput)[0] : $nisNipInput;
        $apiData = $this->schoolApi->validate($nisNipInput) ?? ($isEmailInput ? null : $this->schoolApi->validate($cleanNisNip));

        if ($apiData) {
            $role = match (strtolower($apiData['jenis_pengguna'] ?? 'siswa')) {
                'guru', 'teacher' => 'teacher',
                default           => 'student',
            };

            // Jika dari API Gateway siswa berstatus alumni (graduates=true), tolak login & hapus akun lokal jika ada
            if ($role === 'student' && (!empty($apiData['is_graduated']) || SchoolApiService::isAlumni($apiData))) {
                if ($localUser) {
                    $localUser->delete();
                }
                throw ValidationException::withMessages([
                    'email' => 'Akun Anda telah berstatus Alumni / Akun Anda tidak terdaftar. Pengaksesan Eskasaba Marketplace hanya diperuntukkan bagi siswa/guru aktif.',
                ]);
            }

            // Bagi akun siswa dari API Gateway, login WAJIB menggunakan format email
            if ($role === 'student' && !$isEmailInput) {
                throw ValidationException::withMessages([
                    'email' => 'Siswa wajib menggunakan Email Sekolah (contoh: NIS@sijuna.com atau NIS@smkn1bangsri.sch.id), bukan NIS saja.',
                ]);
            }

            // Izinkan seluruh siswa non-alumni (termasuk siswa PKL) untuk mengakses sistem
            if ($role === 'student') {
                $classRoom = $apiData['class_room'] ?? null;
                if (empty($classRoom)) {
                    $apiData['class_room'] = 'Siswa PKL / Aktif';
                }
            }

            // Jika memasukkan email, email input HARUS cocok persis dengan email resmi dari API
            if ($isEmailInput && !empty($apiData['email']) && strtolower($apiData['email']) !== strtolower($nisNipInput)) {
                throw ValidationException::withMessages([
                    'email' => 'Email atau kata sandi tidak sesuai.',
                ]);
            }

            $extractedPhone = SchoolApiService::extractPhone($apiData);
            $phoneToSave    = ($localUser && !empty($localUser->phone)) ? $localUser->phone : $extractedPhone;

            $userEmail = $apiData['email'] ?? ($localUser ? $localUser->email : null);
            if (empty($userEmail)) {
                $userEmail = $cleanNisNip . '@sijuna.com';
            }

            $isDefaultPw = ($inputPassword === 'password') && ($localUser ? $localUser->is_default_password : true);

            // Update atau buat akun lokal secara otomatis
            $localUser = User::updateOrCreate(
                ['nis_nip' => $apiData['nis_nip']],
                [
                    'username'            => $apiData['nama'],
                    'email'               => $userEmail,
                    'role'                => $role,
                    'class_room'          => $apiData['class_room'] ?? null,
                    'phone'               => $phoneToSave,
                    'api_id'              => $apiData['id'] ?? ($localUser ? $localUser->api_id : 0),
                    'password'            => $isDefaultPw ? ($localUser ? $localUser->password : Hash::make('password')) : Hash::make($inputPassword),
                    'is_default_password' => $isDefaultPw,
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

            return redirect()->route('profile.index')->with('success', 'Berhasil login! Selamat datang kembali, ' . $localUser->username . '.');
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
        try {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Logout session warning: ' . $e->getMessage());
        }

        return redirect()->route('home')->with('success', 'Anda telah berhasil keluar dari akun.');
    }
}