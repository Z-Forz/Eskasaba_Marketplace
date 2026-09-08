<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OAuthController extends Controller
{
    /**
     * Menerima otorisasi SSO otomatis dari Portal SiPintu Gateway
     */
    public function callback(Request $request): RedirectResponse|JsonResponse
    {
        // 1. Tangkap Authorization Code / SSO Token yang dikirim SiPintu
        $code = $request->input('code')
            ?? $request->input('token')
            ?? $request->input('sso_token')
            ?? $request->input('data');

        $directNis = $request->input('nis_nip')
            ?? $request->input('nis')
            ?? $request->input('nip')
            ?? $request->input('email')
            ?? $request->input('username');

        // Jika nis_nip dikirim tetapi nilainya adalah token string panjang / non-numerik (seperti RSsWE6WVEx...), jadikan $code
        if (! $code && $directNis && (strlen($directNis) > 20 || ! is_numeric(str_replace(['@', '.', '-'], '', $directNis)))) {
            $code = $directNis;
            $directNis = null;
        }

        // Fallback untuk direct NIS/NIP/Email callback jika code/token tidak ada
        if (! $code) {
            if ($directNis) {
                return app(\App\Http\Controllers\Auth\SchoolCallbackController::class)->handle($request);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Otorisasi SSO SiPintu gagal: Kode otorisasi tidak ditemukan.',
                ], 400);
            }

            return redirect()->route('login')->with('error', 'Otorisasi SSO SiPintu gagal: Kode otorisasi tidak ditemukan.');
        }

        $baseUrl      = rtrim(env('SIPINTU_BASE_URL', config('services.sipintu.base_url', config('services.sipintu.url', 'https://sipintu.smkn1bangsri.sch.id'))), '/');
        $clientId     = env('SIPINTU_CLIENT_ID', config('services.sipintu.client_id'));
        $clientSecret = env('SIPINTU_CLIENT_SECRET', config('services.sipintu.client_secret'));
        $redirectUri  = env('SIPINTU_REDIRECT_URI', config('services.sipintu.redirect_uri', url('/oauth/callback')));

        // 2. Tukar Code dengan Access Token (Backend-to-Backend HTTP POST)
        $tokenResponse = null;
        $activeBaseUrl = $baseUrl;

        try {
            $tokenResponse = Http::asForm()->acceptJson()->timeout(10)->post("{$activeBaseUrl}/oauth/token", [
                'grant_type'    => 'authorization_code',
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri'  => $redirectUri,
                'code'          => $code,
            ]);
        } catch (\Exception $e) {
            Log::warning("SSO SiPintu connection to {$activeBaseUrl} failed: " . $e->getMessage());

            $altBaseUrl = str_contains($activeBaseUrl, 'localhost') 
                ? 'https://sipintu.smkn1bangsri.sch.id' 
                : 'http://localhost:8000';

            try {
                $tokenResponse = Http::asForm()->acceptJson()->timeout(5)->post("{$altBaseUrl}/oauth/token", [
                    'grant_type'    => 'authorization_code',
                    'client_id'     => $clientId,
                    'client_secret' => $clientSecret,
                    'redirect_uri'  => $redirectUri,
                    'code'          => $code,
                ]);
                if ($tokenResponse->successful()) {
                    $activeBaseUrl = $altBaseUrl;
                }
            } catch (\Exception $eAlt) {
                Log::error("SSO SiPintu fallback connection to {$altBaseUrl} also failed: " . $eAlt->getMessage());
            }
        }

        if (! $tokenResponse || $tokenResponse->failed()) {
            $altBaseUrl = str_contains($activeBaseUrl, 'localhost') 
                ? 'https://sipintu.smkn1bangsri.sch.id' 
                : 'http://localhost:8000';

            try {
                $altResponse = Http::asForm()->acceptJson()->timeout(5)->post("{$altBaseUrl}/oauth/token", [
                    'grant_type'    => 'authorization_code',
                    'client_id'     => $clientId,
                    'client_secret' => $clientSecret,
                    'redirect_uri'  => $redirectUri,
                    'code'          => $code,
                ]);
                if ($altResponse->successful()) {
                    $tokenResponse = $altResponse;
                    $activeBaseUrl = $altBaseUrl;
                }
            } catch (\Exception $eAlt) {
            }
        }

        // 2b. Fallback: jika token exchange gagal, tapi $code dapat digunakan langsung sebagai Bearer token untuk GET /api/v1/user
        if (! $tokenResponse || $tokenResponse->failed()) {
            try {
                $directUserResponse = Http::withToken($code)
                    ->acceptJson()
                    ->timeout(8)
                    ->get("{$activeBaseUrl}/api/v1/user");

                if ($directUserResponse->successful() && ! empty($directUserResponse->json())) {
                    $sipintuUser = $directUserResponse->json('data') ?? $directUserResponse->json();
                    if (! empty($sipintuUser['email']) || ! empty($sipintuUser['nis_nip']) || ! empty($sipintuUser['external_id'])) {
                        return $this->processAuthenticatedUser($request, $sipintuUser);
                    }
                }
            } catch (\Exception $eDirect) {
            }

            // Jika masih gagal dan ada directNis, coba handle via SchoolCallbackController
            if ($directNis) {
                $request->merge(['nis_nip' => $directNis]);
                return app(\App\Http\Controllers\Auth\SchoolCallbackController::class)->handle($request);
            }

            $errorMsg = $tokenResponse?->json('error_description')
                ?? $tokenResponse?->json('message')
                ?? 'Gagal memverifikasi token ke SiPintu Gateway. Pastikan akun terdaftar di Portal SiPintu.';

            Log::error("SSO Token Exchange Failed: {$errorMsg}", [
                'client_id'    => $clientId,
                'redirect_uri' => $redirectUri,
                'response'     => $tokenResponse?->json(),
            ]);

            if ($request->expectsJson()) {
                return response()->json(['status' => false, 'message' => $errorMsg], 400);
            }

            return redirect()->route('login')->with('error', $errorMsg);
        }

        $accessToken = $tokenResponse->json('access_token');
        $tokenPassword = $tokenResponse->json('password') ?? $tokenResponse->json('password_hash');

        // 3. Ambil data profil pengguna dari SiPintu Gateway
        try {
            $userResponse = Http::withToken($accessToken)
                ->acceptJson()
                ->timeout(10)
                ->get("{$activeBaseUrl}/api/v1/user");
        } catch (\Exception $e) {
            Log::error("SSO User Fetch Exception: " . $e->getMessage());
            return redirect()->route('login')->with('error', 'Gagal menghubungi endpoint profil pengguna SiPintu.');
        }

        if ($userResponse->failed()) {
            $userError = $userResponse->json('message') ?? 'Gagal mengambil data akun dari SiPintu Gateway.';
            return redirect()->route('login')->with('error', $userError);
        }

        $sipintuUser = $userResponse->json('data') ?? $userResponse->json();

        return $this->processAuthenticatedUser($request, $sipintuUser, $tokenPassword);
    }

    /**
     * Auto-provisioning & login user dari data profil SiPintu
     */
    protected function processAuthenticatedUser(Request $request, array $sipintuUser, ?string $tokenPassword = null): RedirectResponse|JsonResponse
    {
        $nisNip = $sipintuUser['external_id']
            ?? $sipintuUser['nis_nip']
            ?? $sipintuUser['nis']
            ?? $sipintuUser['nip']
            ?? $sipintuUser['username']
            ?? null;

        $email = $sipintuUser['email'] ?? ($nisNip ? "{$nisNip}@smkn1bangsri.sch.id" : null);
        $name  = $sipintuUser['name'] ?? $sipintuUser['nama'] ?? $sipintuUser['username'] ?? ('User ' . ($nisNip ?? ''));

        $roleRaw = strtolower($sipintuUser['role'] ?? $sipintuUser['jenis_pengguna'] ?? 'student');
        $role    = in_array($roleRaw, ['guru', 'teacher']) ? 'teacher' : 'student';

        $classroom = $sipintuUser['classroom'] ?? $sipintuUser['class_room'] ?? $sipintuUser['kelas'] ?? null;
        $phone     = $sipintuUser['phone'] ?? $sipintuUser['telepon'] ?? null;
        $apiId     = (int) ($sipintuUser['id'] ?? $sipintuUser['sub'] ?? ($nisNip ?: rand(1000, 9999)));

        $passwordHash = $tokenPassword
            ?? $sipintuUser['password']
            ?? $sipintuUser['password_hash']
            ?? null;

        $user = null;
        if ($nisNip) {
            $user = User::where('nis_nip', (string) $nisNip)->first();
        }
        if (! $user && $email) {
            $user = User::where('email', $email)->first();
        }

        if ($user) {
            $updateData = [
                'username' => $name,
                'role'     => $role,
            ];

            if ($classroom) {
                $updateData['class_room'] = $classroom;
            }
            if ($phone) {
                $updateData['phone'] = $phone;
            }
            if ($email && $user->email !== $email) {
                $updateData['email'] = $email;
            }
            if ($nisNip && ! $user->nis_nip) {
                $updateData['nis_nip'] = (string) $nisNip;
            }
            if ($passwordHash && $user->password !== $passwordHash) {
                $updateData['password']             = $passwordHash;
                $updateData['is_default_password'] = false;
            }

            $user->update($updateData);
        } else {
            $user = User::create([
                'username'            => $name,
                'nis_nip'             => $nisNip ? (string) $nisNip : null,
                'email'               => $email ?: ($nisNip ? "{$nisNip}@smkn1bangsri.sch.id" : "user_{$apiId}@sekolah.id"),
                'role'                => $role,
                'class_room'          => $classroom ?? ($role === 'teacher' ? 'Dewan Guru' : null),
                'phone'               => $phone,
                'api_id'              => $apiId,
                'password'            => $passwordHash ?? bcrypt(Str::random(24)),
                'is_default_password' => false,
            ]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        if (class_exists(ActivityLog::class)) {
            ActivityLog::record(
                $user->id,
                'login',
                "Login SSO SiPintu berhasil ({$user->username})",
                $request
            );
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status'   => true,
                'message'  => "Login SSO berhasil. Selamat datang kembali, {$user->username}!",
                'user'     => $user,
                'redirect' => route('dashboard'),
            ]);
        }

        return redirect()->intended(route('dashboard'))->with('success', "Selamat datang kembali, {$user->username}!");
    }

    /**
     * Menerima payload pembaruan data pengguna realtime dari SiPintu Gateway via Webhook
     */
    public function syncUser(Request $request): JsonResponse
    {
        // 1. Verifikasi Keamanan Signature HMAC SHA-256
        $signature = $request->header('X-SiPintu-Signature');
        $clientSecret = env('SIPINTU_CLIENT_SECRET', config('services.sipintu.client_secret'));

        if ($signature && $clientSecret) {
            $computed = hash_hmac('sha256', $request->getContent(), $clientSecret);
            if (! hash_equals($computed, $signature)) {
                return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 401);
            }
        }

        $userData = $request->input('user');
        $previous = $request->input('previous', []);

        if (! $userData) {
            return response()->json(['status' => 'error', 'message' => 'Missing user payload'], 400);
        }

        // 2. Temukan user berdasarkan email atau external_id (NIS/NIP)
        $nisNip = $userData['external_id'] ?? $userData['nis_nip'] ?? $userData['nis'] ?? $userData['nip'] ?? null;
        $email = $userData['email'] ?? null;

        $user = User::query()
            ->when($email, function ($q) use ($email, $previous) {
                $q->where('email', $email);
                if (! empty($previous['email'])) {
                    $q->orWhere('email', $previous['email']);
                }
            })
            ->when($nisNip, function ($q) use ($nisNip) {
                $q->orWhere('nis_nip', (string) $nisNip);
            })
            ->first();

        $roleRaw = strtolower($userData['role'] ?? 'student');
        $role = in_array($roleRaw, ['guru', 'teacher']) ? 'teacher' : 'student';

        // 3. Siapkan data pembaruan
        $updateFields = [
            'username' => $userData['name'] ?? $userData['username'] ?? 'User',
            'role'     => $role,
        ];

        if ($email) {
            $updateFields['email'] = $email;
        }
        if ($nisNip) {
            $updateFields['nis_nip'] = (string) $nisNip;
        }

        // Sinkronkan password hash jika ada
        if (! empty($userData['password'])) {
            $updateFields['password'] = $userData['password'];
            $updateFields['is_default_password'] = false;
        } elseif (! empty($userData['password_hash'])) {
            $updateFields['password'] = $userData['password_hash'];
            $updateFields['is_default_password'] = false;
        }

        if (isset($userData['phone']) || isset($userData['telepon'])) {
            $updateFields['phone'] = $userData['phone'] ?? $userData['telepon'];
        }
        if (isset($userData['classroom']) || isset($userData['class_room']) || isset($userData['kelas'])) {
            $updateFields['class_room'] = $userData['classroom'] ?? $userData['class_room'] ?? $userData['kelas'];
        }

        // 4. Update jika user sudah ada, atau buat baru jika belum pernah login
        if ($user) {
            $user->update($updateFields);
            $action = 'updated';
        } else {
            $updateFields['api_id'] = (int) ($userData['id'] ?? ($nisNip ?: rand(1000, 9999)));
            if (empty($updateFields['password'])) {
                $updateFields['password'] = bcrypt(Str::random(24));
                $updateFields['is_default_password'] = false;
            }
            $user = User::create($updateFields);
            $action = 'created';
        }

        return response()->json([
            'status'  => 'success',
            'action'  => $action,
            'message' => "User {$user->username} ({$user->email}) berhasil disinkronkan di Eskasaba Marketplace.",
            'user_id' => $user->id,
        ]);
    }
}
