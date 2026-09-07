<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\SchoolApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OAuthController extends Controller
{
    public function __construct(protected SchoolApiService $schoolApi) {}

    /**
     * Menerima kiriman otorisasi / SSO Callback dari Portal SiPintu.
     */
    public function callback(Request $request): RedirectResponse|JsonResponse
    {
        Log::info('OAuth SSO Callback request received:', $request->all());

        $code = $request->input('code');

        $nisNipDirect = $request->input('nis_nip')
            ?? $request->input('nis')
            ?? $request->input('nip')
            ?? $request->input('user_id')
            ?? $request->input('id');

        $baseUrl = rtrim(config('services.sipintu.base_url', env('SIPINTU_BASE_URL', 'https://sipintu.smkn1bangsri.sch.id')), '/');
        $clientId = config('services.sipintu.client_id', env('SIPINTU_CLIENT_ID', 'app_2o8jtpekzdeh'));
        $clientSecret = config('services.sipintu.client_secret', env('SIPINTU_CLIENT_SECRET', 'sec_BpEVnzLBIIP4eR4cdjhXHtdPF67Dj3OO'));
        $redirectUri = config('services.sipintu.redirect_uri', env('SIPINTU_REDIRECT_URI', url('/oauth/callback')));

        $sipintuUser = null;

        if ($code) {
            // 2. Tukar kode dengan Access Token (Server-to-Server)
            try {
                $tokenResponse = Http::withoutVerifying()->asForm()->acceptJson()->post("{$baseUrl}/oauth/token", [
                    'grant_type'    => 'authorization_code',
                    'client_id'     => $clientId,
                    'client_secret' => $clientSecret,
                    'redirect_uri'  => $redirectUri,
                    'code'          => $code,
                ]);

                if ($tokenResponse->successful()) {
                    $accessToken = $tokenResponse->json('access_token');

                    // 3. Ambil data profil siswa/guru dari endpoint SiPintu
                    $userResponse = Http::withoutVerifying()->withToken($accessToken)
                        ->acceptJson()
                        ->get("{$baseUrl}/api/v1/user");

                    if ($userResponse->successful()) {
                        $sipintuUser = $userResponse->json('data') ?? $userResponse->json();
                    }
                } else {
                    Log::warning("OAuth token exchange failed HTTP {$tokenResponse->status()}: " . $tokenResponse->body());
                }
            } catch (\Exception $e) {
                Log::warning("OAuth token exchange exception: " . $e->getMessage());
            }
        }

        // 4. Cocokkan dengan data siswa/guru yang SUDAH ADA di database lokal downstream
        //    (Bisa menggunakan NIS / external_id atau Email)
        $nisNip = $sipintuUser['external_id']
            ?? $sipintuUser['nis_nip']
            ?? $sipintuUser['nis']
            ?? $sipintuUser['nip']
            ?? $nisNipDirect;

        if (! empty($nisNip) && is_string($nisNip) && str_contains($nisNip, '@')) {
            $nisNip = explode('@', $nisNip)[0];
        }

        $email = $sipintuUser['email'] ?? $request->input('email');

        $user = null;
        if (! empty($nisNip) || ! empty($email)) {
            $query = User::query();
            if (! empty($nisNip)) {
                $query->where('nis_nip', (string) $nisNip)
                    ->orWhere('api_id', (string) $nisNip);
            }
            if (! empty($email)) {
                $query->orWhere('email', (string) $email);
            }
            $user = $query->first();
        }

        // Jika user belum ada di DB lokal, coba sinkronisasi via SchoolApiService
        if (! $user && ! empty($nisNip)) {
            $apiData = $this->schoolApi->validate((string) $nisNip);
            if ($apiData) {
                $role = match (strtolower($apiData['jenis_pengguna'] ?? 'siswa')) {
                    'guru', 'teacher' => 'teacher',
                    default           => 'student',
                };

                $isJunior = preg_match('/^(X|XI)\s/i', trim((string) ($apiData['class_room'] ?? '')));
                $defaultDomain = $isJunior ? 'sijuna.com' : 'smkn1bangsri.sch.id';

                $classRoom = $apiData['class_room'] ?? null;
                if ($role === 'teacher' && empty($classRoom)) {
                    $classRoom = 'Dewan Guru';
                }

                $user = User::updateOrCreate(
                    ['nis_nip' => (string) $apiData['nis_nip']],
                    [
                        'username'            => $apiData['nama'],
                        'email'               => $apiData['email'] ?? ($apiData['nis_nip'] . '@' . $defaultDomain),
                        'role'                => $role,
                        'class_room'          => $classRoom,
                        'api_id'              => $apiData['id'] ?? null,
                        'phone'               => $apiData['telepon'] ?? null,
                        'password'            => Hash::make('password'),
                        'is_default_password' => true,
                    ]
                );
            }
        }

        if (! $user) {
            $identifier = $nisNip ?? $email ?? 'tidak diketahui';
            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => false,
                    'message' => "Login SSO SiPintu Gagal: Akun siswa/guru ({$identifier}) tidak terdaftar pada aplikasi ini.",
                ], 404);
            }

            return redirect()->route('login')->with('error', "Login SSO SiPintu Gagal: Akun siswa/guru ({$identifier}) tidak terdaftar pada aplikasi ini.");
        }

        // 5. Autentikasikan sesi lokal & arahkan ke dashboard
        Auth::login($user, true);

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        ActivityLog::record(
            $user->id,
            'login',
            "Login otomatis berhasil via SiPintu OAuth SSO Callback (IP: {$request->ip()})",
            $request
        );

        if ($request->expectsJson()) {
            return response()->json([
                'status'   => true,
                'message'  => 'Login SSO SiPintu berhasil.',
                'user'     => $user,
                'redirect' => route('dashboard'),
            ]);
        }

        return redirect()->intended(route('dashboard'))->with('success', "Selamat datang, {$user->username}!");
    }
}
