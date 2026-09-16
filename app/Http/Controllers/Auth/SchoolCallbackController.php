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
            if ($localUser->role === 'student' && \App\Services\SchoolApiService::isAlumni($localUser->toArray())) {
                $localUser->delete();
                if ($request->expectsJson()) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Akun Anda telah berstatus Alumni (Lulus). Pengaksesan Eskasaba Marketplace hanya diperuntukkan bagi siswa/guru aktif.',
                    ], 403);
                }
                return redirect()->route('login')->with('error', 'Akun Anda telah berstatus Alumni (Lulus). Pengaksesan Eskasaba Marketplace hanya diperuntukkan bagi siswa/guru aktif.');
            }

            Auth::login($localUser, true);
            $request->session()->regenerate();

            if ($request->expectsJson()) {
                return response()->json([
                    'status'   => true,
                    'message'  => 'Berhasil login.',
                    'user'     => $localUser,
                    'redirect' => route('profile.index'),
                ]);
            }

            return redirect()->route('profile.index')->with('success', "Berhasil login! Selamat datang kembali, {$localUser->username}.");
        }

        // 3. Jika belum ada di lokal, validasi ke SiPintu API / Dataset Sekolah
        $apiData = $this->schoolApi->validate($cleanIdentifier);

        if (! $apiData) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Pengguna tidak terdaftar pada API Sekolah atau akun Anda berstatus Alumni (Lulus).',
                ], 404);
            }

            return redirect()->route('login')->with('error', "Login SSO SiPintu Gagal: Akun ({$nisNip}) tidak terdaftar atau berstatus Alumni.");
        }

        $role = ($apiData['jenis_pengguna'] ?? 'siswa') === 'guru' ? 'teacher' : 'student';

        if ($role === 'student') {
            if (!empty($apiData['is_graduated']) || \App\Services\SchoolApiService::isAlumni($apiData)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Akun Anda telah berstatus Alumni (Lulus). Pengaksesan Eskasaba Marketplace hanya diperuntukkan bagi siswa/guru aktif.',
                    ], 403);
                }
                return redirect()->route('login')->with('error', 'Akun Anda telah berstatus Alumni (Lulus). Pengaksesan Eskasaba Marketplace hanya diperuntukkan bagi siswa/guru aktif.');
            }

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
                'message'  => 'Berhasil login.',
                'user'     => $user,
                'redirect' => route('profile.index'),
            ]);
        }

        return redirect()->route('profile.index')->with('success', "Berhasil login! Selamat datang kembali, {$user->username}.");
    }
}
