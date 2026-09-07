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
        // Jika request membawa authorization code OAuth 2.0 dari SiPintu Gateway, delegasikan ke OAuthController
        if ($request->has('code')) {
            return app(\App\Http\Controllers\OAuthController::class)->callback($request);
        }

        $nisNip = $request->input('nis_nip') ?? $request->input('nis') ?? $request->input('nip');
        $token  = $request->input('token');

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

        $user = User::updateOrCreate(
            ['nis_nip' => $apiData['nis_nip']],
            [
                'username'            => $apiData['nama'],
                'email'               => $apiData['email'] ?? ($apiData['nis_nip'] . '@sekolah.id'),
                'role'                => $role,
                'class_room'           => $apiData['class_room'] ?? null,
                'phone'               => $apiData['telepon'] ?? null,
                'api_id'              => $apiData['id'] ?? null,
                'password'            => Hash::make('password'),
                'is_default_password' => true,
            ]
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
