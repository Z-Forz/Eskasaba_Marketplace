<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use App\Services\SchoolApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SchoolCallbackController extends Controller
{
    public function __construct(protected SchoolApiService $schoolApi) {}

    /**
     * Handle callback request from School API / SSO (SiPintu Gateway).
     */
    public function handle(Request $request): RedirectResponse|JsonResponse
    {
        Log::info('SchoolCallback request received:', $request->all());

        // Extract NIS/NIP or identifier from all possible payload keys sent by SiPintu / Gateway
        $nisNip = $request->input('nis_nip')
            ?? $request->input('nis')
            ?? $request->input('nip')
            ?? $request->input('user_id')
            ?? $request->input('id')
            ?? $request->input('username')
            ?? $request->input('email')
            ?? $request->input('code')
            ?? $request->input('token')
            ?? $request->input('access_token')
            ?? $request->input('sso_token')
            ?? $request->input('ticket')
            ?? $request->input('data.nis_nip')
            ?? $request->input('data.nis')
            ?? $request->input('data.nip')
            ?? $request->input('data.id')
            ?? $request->input('user.nis_nip')
            ?? $request->input('user.nis')
            ?? $request->input('user.nip')
            ?? $request->input('user.id')
            ?? $request->input('payload.nis_nip');

        if (! empty($nisNip) && is_string($nisNip) && str_contains($nisNip, '@')) {
            $nisNip = explode('@', $nisNip)[0];
        }

        $user = null;

        // 1. Cari dulu pengguna dari database lokal jika NIS/NIP atau Email sudah ada
        if (! empty($nisNip)) {
            $user = User::where('nis_nip', (string) $nisNip)
                ->orWhere('email', (string) $nisNip)
                ->orWhere('email', $nisNip . '@smkn1bangsri.sch.id')
                ->orWhere('email', $nisNip . '@sijuna.com')
                ->orWhere('api_id', (string) $nisNip)
                ->first();
        }

        // 2. Validasi & Ambil data dari SiPintu API Gateway
        $apiData = null;
        if (! empty($nisNip)) {
            $apiData = $this->schoolApi->validate((string) $nisNip);
        }

        // 3. Jika API SiPintu mengembalikan data valid, buat/update akun lokal
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

            $userData = [
                'username'   => $apiData['nama'],
                'email'      => $user?->email ?? $apiData['email'] ?? ($apiData['nis_nip'] . '@' . $defaultDomain),
                'role'       => $role,
                'class_room' => $classRoom,
                'api_id'     => $apiData['id'] ?? null,
            ];

            if (! $user) {
                $userData['password'] = Hash::make('password');
                $userData['is_default_password'] = true;
            }

            if (! empty($apiData['telepon'])) {
                $userData['phone'] = $apiData['telepon'];
            }

            $user = User::updateOrCreate(
                ['nis_nip' => (string) $apiData['nis_nip']],
                $userData
            );
        }

        // 4. Jika user tidak ditemukan sama sekali (baik dari API maupun DB lokal)
        if (! $user) {
            $identifierInfo = ! empty($nisNip) ? " (NIS/NIP: {$nisNip})" : "";
            $failMessage = "Login SSO SiPintu Gagal: Akun{$identifierInfo} tidak terdaftar di sistem sekolah.";

            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => false,
                    'message' => $failMessage,
                ], 404);
            }

            return redirect()->route('login')->with('error', $failMessage);
        }

        // 5. AUTO-LOGIN KAN PENGGUNA & REGENERATE SESSION
        Auth::login($user, true);

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        ActivityLog::record(
            $user->id,
            'login',
            "Login otomatis berhasil via SiPintu SSO Callback (IP: {$request->ip()})",
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

        return redirect()->route('dashboard')->with('success', 'Selamat datang! Berhasil masuk via SiPintu SSO.');
    }
}
