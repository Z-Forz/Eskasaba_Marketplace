<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\SchoolApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
        if (Auth::guard('web')->check()) {
            return redirect()->route('dashboard');
        }

        // Jika request membawa parameter SSO dari Portal SiPintu, proses SSO login secara otomatis
        $ssoParams = ['code', 'token', 'sso_token', 'nis_nip', 'nis', 'nip', 'email', 'username', 'data'];
        foreach ($ssoParams as $param) {
            if ($request->has($param) && ! empty($request->input($param))) {
                return app(SchoolCallbackController::class)->handle($request);
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
        $cleanNisNip = $isEmailInput ? explode('@', $nisNipInput)[0] : $nisNipInput;

        // 1. Cari user di database lokal berdasarkan nis_nip, email, atau username
        $localUser = User::where('nis_nip', (string) $nisNipInput)
            ->orWhere('nis_nip', (string) $cleanNisNip)
            ->orWhere('email', $nisNipInput)
            ->orWhere('username', $nisNipInput)
            ->first();

        // 2. Ambil / Validasi data terbaru dari API Gateway SiPintu
        $searchKey = ($localUser && ! empty($localUser->nis_nip)) ? $localUser->nis_nip : $nisNipInput;
        $apiData = $this->schoolApi->validate($searchKey)
            ?? ($searchKey !== $nisNipInput ? $this->schoolApi->validate($nisNipInput) : null)
            ?? ($isEmailInput ? $this->schoolApi->validate($cleanNisNip) : null);

        // Jika tidak ditemukan di lokal dan API SiPintu juga tidak ada
        if (! $localUser && ! $apiData) {
            throw ValidationException::withMessages([
                'email' => 'NIS/NIP, Email, atau kata sandi tidak sesuai.',
            ]);
        }

        // Tentukan role dari API SiPintu atau data lokal
        $role = 'student';
        if ($apiData) {
            $role = match (strtolower($apiData['jenis_pengguna'] ?? 'siswa')) {
                'guru', 'teacher', 'dewan guru' => 'teacher',
                default => 'student',
            };
        } elseif ($localUser) {
            $role = $localUser->role;
        }

        // Bagi akun siswa, login WAJIB menggunakan format email sekolah resmi
        if ($role === 'student' && ! $isEmailInput) {
            throw ValidationException::withMessages([
                'email' => 'Siswa wajib menggunakan Email Sekolah ( email atau nis yang digunakan saat login sijuna, contoh: 1234@smkn1bangsri.sch.id / 1234@sijuna.com ).',
            ]);
        }

        // Tolak jika akun siswa berstatus alumni / lulus
        $isAlumni = false;
        if ($apiData) {
            $isAlumni = (! empty($apiData['is_graduated']) || SchoolApiService::isAlumni($apiData));
        } elseif ($localUser && $localUser->role === 'student') {
            $isAlumni = SchoolApiService::isAlumni($localUser->toArray());
        }

        if ($role === 'student' && $isAlumni) {
            if ($localUser) {
                $localUser->delete();
            }
            throw ValidationException::withMessages([
                'email' => 'Akun Anda telah berstatus Alumni / Akun Anda tidak terdaftar. Pengaksesan Eskasaba Marketplace hanya diperuntukkan bagi siswa/guru aktif.',
            ]);
        }

        // Jika localUser belum ketemu lewat pencarian input, tapi apiData memberikan nis_nip / email, cari ulang di DB
        if (! $localUser && $apiData) {
            $apiNisNip = (string) ($apiData['nis_nip'] ?? '');
            $apiEmail = $apiData['email'] ?? null;

            $localUser = User::query()
                ->when($apiNisNip, fn ($q) => $q->where('nis_nip', $apiNisNip))
                ->when($apiEmail, fn ($q) => $q->orWhere('email', $apiEmail))
                ->first();
        }

        // Verifikasi bahwa apiData cocok dengan localUser jika localUser sudah ditemukan
        if ($localUser && $apiData) {
            $apiNisNip = (string) ($apiData['nis_nip'] ?? '');
            $apiEmail = strtolower(trim((string) ($apiData['email'] ?? '')));
            $localEmail = strtolower(trim((string) $localUser->email));

            if ($apiNisNip !== '' && $apiNisNip !== (string) $localUser->nis_nip && ($apiEmail === '' || $apiEmail !== $localEmail)) {
                Log::warning("SchoolLoginController: Ignored mismatched apiData ({$apiNisNip}) for localUser ({$localUser->nis_nip})");
                $apiData = null;
            }
        }

        // 3. Verifikasi Kata Sandi (Password Check)
        $isPasswordValid = false;

        if ($localUser) {
            // Check terhadap password di DB lokal
            if (Hash::check($inputPassword, $localUser->password)) {
                $isPasswordValid = true;
            } elseif ($localUser->is_default_password && $inputPassword === 'password') {
                $isPasswordValid = true;
            }
        }

        // Jika password di API SiPintu dikirim dan cocok
        if (! $isPasswordValid && $apiData && ! empty($apiData['password'])) {
            $apiPwd = $apiData['password'];
            if (str_starts_with($apiPwd, '$2y$') || str_starts_with($apiPwd, '$2a$') || str_starts_with($apiPwd, '$2b$') || str_starts_with($apiPwd, '$argon2id$')) {
                $isPasswordValid = Hash::check($inputPassword, $apiPwd);
            } else {
                $isPasswordValid = ($inputPassword === $apiPwd);
            }
        }

        // Jika data dari API SiPintu terverifikasi (siswa/guru aktif) dan kata sandi diisi, izinkan autentikasi & sinkronkan kata sandi baru
        if (! $isPasswordValid && $apiData && ! empty($inputPassword)) {
            $isPasswordValid = true;
        }

        // Untuk user baru (belum ada di DB lokal), password default harus 'password' atau cocok dengan API
        if (! $localUser && ! $isPasswordValid) {
            if ($inputPassword === 'password') {
                $isPasswordValid = true;
            }
        }

        if (! $isPasswordValid) {
            throw ValidationException::withMessages([
                'email' => 'NIS/NIP, Email, atau kata sandi tidak sesuai.',
            ]);
        }

        // 4. Sinkronisasi Data Profil (Email, Username, Role, ClassRoom, Password)
        // KECUALI nomor WhatsApp (phone) yang SUDAH TERISI di Eskasaba Marketplace
        $targetNisNip = (string) ($localUser?->nis_nip ?? $apiData['nis_nip'] ?? $cleanNisNip);
        $userEmail = $apiData['email'] ?? $localUser?->email ?? ($cleanNisNip.'@sijuna.com');

        // Pastikan tidak tabrakan email dengan user lain
        $existingEmailUser = User::where('email', $userEmail)->where('nis_nip', '!=', $targetNisNip)->first();
        if ($existingEmailUser) {
            $userEmail = $targetNisNip.'@sijuna.com';
        }

        // Prioritaskan nomor WA lokal jika sudah terisi, jika belum terisi baru pakai dari SiPintu
        $finalPhone = ! empty($localUser?->phone) ? $localUser->phone : ($apiData['telepon'] ?? null);

        $updatePayload = [
            'username' => $apiData['nama'] ?? $localUser?->username ?? ('User '.$targetNisNip),
            'email' => $userEmail,
            'role' => $role,
            'class_room' => $apiData['class_room'] ?? $localUser?->class_room ?? ($role === 'student' ? 'Siswa PKL / Aktif' : 'Dewan Guru'),
            'phone' => $finalPhone,
            'api_id' => $apiData['id'] ?? $localUser?->api_id ?? 0,
        ];

        // Update password jika user login dengan password baru atau jika default password diubah
        if (! $localUser) {
            $updatePayload['password'] = Hash::make($inputPassword);
            $updatePayload['plain_password'] = $inputPassword;
            $updatePayload['is_default_password'] = ($inputPassword === 'password');
        } elseif (! Hash::check($inputPassword, $localUser->password) && $isPasswordValid) {
            // User berhasil terautentikasi dengan password baru dari SiPintu
            $updatePayload['password'] = Hash::make($inputPassword);
            $updatePayload['plain_password'] = $inputPassword;
            $updatePayload['is_default_password'] = ($inputPassword === 'password');
        }

        // Jalankan updateOrCreate secara aman agar tidak memicu UniqueConstraintViolationException
        $localUser = User::updateOrCreate(
            ['nis_nip' => $targetNisNip],
            $updatePayload
        );

        // 5. Login Session
        Auth::guard('web')->login($localUser);
        $request->session()->regenerate();

        if (class_exists(ActivityLog::class)) {
            ActivityLog::record(
                $localUser->id,
                'login',
                "Login berhasil dari IP {$request->ip()}",
                $request
            );
        }

        return redirect()->route('profile.index')->with('success', 'Berhasil login! Selamat datang kembali, '.$localUser->username.'.');
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
            Log::warning('Logout session warning: '.$e->getMessage());
        }

        return redirect()->route('home')->with('success', 'Anda telah berhasil keluar dari akun.');
    }
}
