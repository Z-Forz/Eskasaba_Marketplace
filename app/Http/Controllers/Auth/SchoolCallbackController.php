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
        $nisNip = $request->input('nis_nip') ?? $request->input('nis') ?? $request->input('nip') ?? $request->input('user_id');
        $token  = $request->input('token') ?? $request->input('code');

        if ($nisNip && str_contains($nisNip, '@')) {
            $nisNip = explode('@', $nisNip)[0];
        }

        if (! $nisNip) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'NIS/NIP is required in callback parameter.',
                ], 400);
            }

            return redirect()->route('login')->with('error', 'Callback API Sekolah gagal: parameter NIS/NIP tidak ditemukan.');
        }

        $apiData = $this->schoolApi->validate($nisNip);

        if (! $apiData) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Pengguna tidak terdaftar pada API Sekolah.',
                ], 404);
            }

            return redirect()->route('login')->with('error', 'Pengguna tidak ditemukan di Database Sekolah.');
        }

        $role = ($apiData['jenis_pengguna'] ?? 'siswa') === 'guru' ? 'teacher' : 'student';

        $isJunior = preg_match('/^(X|XI)\s/i', trim((string) ($apiData['class_room'] ?? '')));
        $defaultDomain = $isJunior ? 'sijuna.com' : 'smkn1bangsri.sch.id';

        $userData = [
            'username'            => $apiData['nama'],
            'email'               => $apiData['email'] ?? ($apiData['nis_nip'] . '@' . $defaultDomain),
            'role'                => $role,
            'class_room'          => $apiData['class_room'] ?? null,
            'api_id'              => $apiData['id'] ?? null,
            'password'            => Hash::make('password'),
            'is_default_password' => true,
        ];

        if (!empty($apiData['telepon'])) {
            $userData['phone'] = $apiData['telepon'];
        }

        $user = User::updateOrCreate(
            ['nis_nip' => $apiData['nis_nip']],
            $userData
        );

        Auth::login($user, true);

        if ($request->expectsJson()) {
            return response()->json([
                'status'   => true,
                'message'  => 'Login callback berhasil.',
                'user'     => $user,
                'redirect' => route('dashboard'),
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Berhasil masuk via Callback SSO Sekolah.');
    }
}
