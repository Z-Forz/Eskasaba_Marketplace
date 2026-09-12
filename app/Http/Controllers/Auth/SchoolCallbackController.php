<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SchoolApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SchoolCallbackController extends Controller
{
    protected SchoolApiService $schoolApi;

    public function __construct(SchoolApiService $schoolApi)
    {
        $this->schoolApi = $schoolApi;
    }

    /**
     * Handle callback request from School API / SSO.
     */
    public function handle(Request $request): RedirectResponse|JsonResponse
    {
        // 1. Tangkap semua variasi parameter SSO
        $code = $request->input('code')
            ?? $request->input('token')
            ?? $request->input('sso_token')
            ?? $request->input('data');

        $nisNip = $request->input('nis_nip')
            ?? $request->input('nis')
            ?? $request->input('nip')
            ?? $request->input('email')
            ?? $request->input('username');

        // Jika nis_nip dikirim tetapi nilainya adalah token string panjang / non-numerik (seperti RSsWE6WVEx...), jadikan $code
        if (! $code && $nisNip && (strlen($nisNip) > 20 || ! is_numeric(str_replace(['@', '.', '-'], '', $nisNip)))) {
            $code = $nisNip;
            $nisNip = null;
        }

        // Jika request membawa authorization code / SSO token, delegasikan ke OAuthController
        if ($code) {
            $request->merge(['code' => $code]);
            return app(\App\Http\Controllers\OAuthController::class)->callback($request);
        }

        if (! $nisNip) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Parameter NIS/NIP/Email wajib diisi untuk SSO.',
                ], 400);
            }

            return redirect()->route('login')->with('error', 'Callback SSO SiPintu gagal: parameter NIS/NIP/Email tidak ditemukan.');
        }

        $cleanIdentifier = trim($nisNip);
        $extractedNis = str_contains($cleanIdentifier, '@') ? explode('@', $cleanIdentifier)[0] : $cleanIdentifier;

        // 2. Cek apakah pengguna sudah ada di database lokal terlebih dahulu
        $localUser = User::where('nis_nip', (string) $cleanIdentifier)
            ->orWhere('nis_nip', (string) $extractedNis)
            ->orWhere('email', $cleanIdentifier)
            ->orWhere('email', 'like', $extractedNis . '@%')
            ->first();

        if ($localUser) {
            Auth::login($localUser, true);
            $request->session()->regenerate();

            if ($request->expectsJson()) {
                return response()->json([
                    'status'   => true,
                    'message'  => 'Login SSO berhasil.',
                    'user'     => $localUser,
                    'redirect' => route('dashboard'),
                ]);
            }

            return redirect()->intended(route('dashboard'))->with('success', "Selamat datang kembali, {$localUser->username}!");
        }

        // 3. Jika belum ada di lokal, validasi ke SiPintu API / Dataset Sekolah
        $apiData = $this->schoolApi->validate($cleanIdentifier);

        if (! $apiData) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Pengguna tidak terdaftar pada API Sekolah.',
                ], 404);
            }

            return redirect()->route('login')->with('error', "Login SSO SiPintu Gagal: Akun ({$nisNip}) tidak terdaftar di sistem sekolah.");
        }

        $role = ($apiData['jenis_pengguna'] ?? 'siswa') === 'guru' ? 'teacher' : 'student';

        if ($role === 'student') {
            $classRoom = $apiData['class_room'] ?? null;
            if (empty($classRoom) || !preg_match('/^(kelas\s+|kls\s+)?(X|XI|XII|10|11|12)(\s+|-|:|$)/i', trim((string) $classRoom))) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Hanya siswa aktif (Kelas 10, 11, dan 12) yang dapat mengakses sistem.',
                    ], 403);
                }

                return redirect()->route('login')->with('error', 'Hanya siswa aktif (Kelas 10, 11, dan 12) yang dapat mengakses sistem.');
            }
        }

        $user = User::updateOrCreate(
            ['nis_nip' => $apiData['nis_nip']],
            [
                'username'            => $apiData['nama'],
                'email'               => $apiData['email'] ?? ($apiData['nis_nip'] . '@smkn1bangsri.sch.id'),
                'role'                => $role,
                'class_room'          => $apiData['class_room'] ?? null,
                'phone'               => $apiData['telepon'] ?? null,
                'api_id'              => $apiData['id'] ?? null,
                'password'            => Hash::make('password'),
                'is_default_password' => true,
            ]
        );

        Auth::login($user, true);
        $request->session()->regenerate();

        if ($request->expectsJson()) {
            return response()->json([
                'status'   => true,
                'message'  => 'Login callback berhasil.',
                'user'     => $user,
                'redirect' => route('dashboard'),
            ]);
        }

        return redirect()->intended(route('dashboard'))->with('success', "Selamat datang kembali, {$user->username}!");
    }
}
