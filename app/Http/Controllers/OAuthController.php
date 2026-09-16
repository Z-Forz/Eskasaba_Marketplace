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
        // 1. Tangkap Authorization Code yang dikirim SiPintu
        $code = $request->query('code') ?? $request->input('code');

        if (! $code) {
            $errorMsg = 'Otorisasi SSO SiPintu gagal: Kode otorisasi (code) tidak ditemukan.';
            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => false,
                    'message' => $errorMsg,
                ], 400);
            }

            return redirect()->route('login')->with('error', $errorMsg);
        }

        $baseUrl      = rtrim(config('services.sipintu.base_url', env('SIPINTU_BASE_URL', 'https://sipintu.smkn1bangsri.sch.id')), '/');
        $clientId     = config('services.sipintu.client_id', env('SIPINTU_CLIENT_ID', ''));
        $clientSecret = config('services.sipintu.client_secret', env('SIPINTU_CLIENT_SECRET', ''));
        $redirectUri  = config('services.sipintu.redirect_uri', env('SIPINTU_REDIRECT_URI', url('/oauth/callback')));

        // 2. Tukar Code dengan Access Token (Backend-to-Backend HTTP POST)
        $tokenResponse = null;

        try {
            $tokenResponse = Http::asForm()
                ->acceptJson()
                ->timeout(10)
                ->post("{$baseUrl}/oauth/token", [
                    'grant_type'    => 'authorization_code',
                    'client_id'     => $clientId,
                    'client_secret' => $clientSecret,
                    'redirect_uri'  => $redirectUri,
                    'code'          => $code,
                ]);
        } catch (\Exception $e) {
            Log::error('SSO Token Exchange Connection Exception: ' . $e->getMessage());
        }

        if (! $tokenResponse || $tokenResponse->failed()) {
            $errorMsg = $tokenResponse?->json('error_description')
                ?? $tokenResponse?->json('message')
                ?? 'Otorisasi SSO gagal: Kode otorisasi tidak valid atau telah kadaluarsa.';

            Log::error("SSO Token Exchange Failed: {$errorMsg}", [
                'client_id'    => $clientId,
                'redirect_uri' => $redirectUri,
            ]);

            if ($request->expectsJson()) {
                return response()->json(['status' => false, 'message' => $errorMsg], 400);
            }

            return redirect()->route('login')->with('error', $errorMsg);
        }

        $accessToken = $tokenResponse->json('access_token');
        if (! $accessToken) {
            $errorMsg = 'Otorisasi SSO gagal: Access token tidak ditemukan dari server SiPintu.';
            if ($request->expectsJson()) {
                return response()->json(['status' => false, 'message' => $errorMsg], 400);
            }

            return redirect()->route('login')->with('error', $errorMsg);
        }

        // 3. Ambil data profil pengguna dari SiPintu Gateway
        try {
            $userResponse = Http::withToken($accessToken)
                ->acceptJson()
                ->timeout(10)
                ->get("{$baseUrl}/api/v1/user");
        } catch (\Exception $e) {
            Log::error('SSO User Fetch Exception: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['status' => false, 'message' => 'Gagal menghubungi endpoint profil pengguna SiPintu.'], 500);
            }

            return redirect()->route('login')->with('error', 'Gagal menghubungi endpoint profil pengguna SiPintu.');
        }

        if ($userResponse->failed()) {
            $userError = $userResponse->json('message') ?? 'Gagal mengambil data akun dari SiPintu Gateway.';

            if ($request->expectsJson()) {
                return response()->json(['status' => false, 'message' => $userError], 400);
            }

            return redirect()->route('login')->with('error', $userError);
        }

        $sipintuUser = $userResponse->json('data') ?? $userResponse->json();

        return $this->processAuthenticatedUser($request, $sipintuUser);
    }

    /**
     * Pencocokan user SiPintu dengan database lokal & login session
     */
    protected function processAuthenticatedUser(Request $request, array $sipintuUser): RedirectResponse|JsonResponse
    {
        $externalId = $sipintuUser['external_id'] ?? null;
        $nisNip     = $sipintuUser['nis_nip']
            ?? $sipintuUser['nis']
            ?? $sipintuUser['nip']
            ?? $externalId
            ?? $sipintuUser['username']
            ?? null;

        $email = $sipintuUser['email'] ?? null;

        // Cocokkan user yang SUDAH ADA di database lokal berdasarkan nis_nip atau email
        $user = User::query()
            ->when($nisNip, function ($query) use ($nisNip) {
                $query->where('nis_nip', (string) $nisNip);
            })
            ->when($email, function ($query) use ($email) {
                $query->orWhere('email', $email);
            })
            ->first();

        // Jika user tidak ditemukan, tolak login SSO
        if (! $user) {
            $identifier = $nisNip ?? $email ?? 'Pengguna';
            $errorMsg   = "Akun SiPintu Anda ({$identifier}) belum terdaftar pada aplikasi Eskasaba Marketplace. Silakan hubungi administrator.";

            Log::warning("SSO Login Rejected: User {$identifier} not found in local database.");

            if ($request->expectsJson()) {
                return response()->json(['status' => false, 'message' => $errorMsg], 403);
            }

            return redirect()->route('login')->with('error', $errorMsg);
        }

        // Perbarui data profil non-sensitif jika ada perubahan dari SiPintu
        $updateData = [];
        if (! empty($sipintuUser['name']) || ! empty($sipintuUser['nama'])) {
            $updateData['username'] = $sipintuUser['name'] ?? $sipintuUser['nama'];
        }
        $classroom = $sipintuUser['classroom'] ?? $sipintuUser['class_room'] ?? $sipintuUser['kelas'] ?? null;
        if ($classroom) {
            $updateData['class_room'] = $classroom;
        }
        $phone = $sipintuUser['phone'] ?? $sipintuUser['telepon'] ?? null;
        if ($phone) {
            $updateData['phone'] = $phone;
        }
        if ($email && $user->email !== $email) {
            $updateData['email'] = $email;
        }
        if ($nisNip && ! $user->nis_nip) {
            $updateData['nis_nip'] = (string) $nisNip;
        }

        if (! empty($updateData)) {
            $user->update($updateData);
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
                'redirect' => route('profile.index'),
            ]);
        }

        return redirect()->intended(route('profile.index'))->with('success', "Berhasil login! Selamat datang kembali, {$user->username}!");
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
