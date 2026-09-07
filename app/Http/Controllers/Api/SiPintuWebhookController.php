<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SiPintuWebhookController extends Controller
{
    /**
     * Handle incoming password sync webhook from SiPintu Gateway.
     */
    public function syncPassword(Request $request): JsonResponse
    {
        Log::info('SiPintu syncPassword webhook received:', $request->all());

        // Verifikasi Client ID & Client Secret jika dikirim dalam header/payload
        $clientId = $request->header('X-Client-ID') ?? $request->input('client_id');
        $clientSecret = $request->header('X-Client-Secret') ?? $request->input('client_secret');

        $expectedClientId = config('services.sipintu.client_id');
        $expectedClientSecret = config('services.sipintu.client_secret');

        if ($clientId && $clientSecret) {
            if ($clientId !== $expectedClientId || $clientSecret !== $expectedClientSecret) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Unauthorized: Invalid Client ID or Client Secret.',
                ], 401);
            }
        }

        $nisNip = $request->input('nis_nip')
            ?? $request->input('nis')
            ?? $request->input('nip')
            ?? $request->input('username')
            ?? $request->input('user_id');

        $newPassword = $request->input('password')
            ?? $request->input('password_hash')
            ?? $request->input('hash')
            ?? $request->input('new_password');

        if (! $nisNip || ! $newPassword) {
            return response()->json([
                'status'  => false,
                'message' => 'Parameter nis_nip dan password wajib diisi.',
            ], 400);
        }

        if (str_contains($nisNip, '@')) {
            $nisNip = explode('@', $nisNip)[0];
        }

        $user = User::where('nis_nip', $nisNip)->first();

        if (! $user) {
            return response()->json([
                'status'  => false,
                'message' => "Pengguna dengan NIS/NIP {$nisNip} tidak ditemukan di downstream.",
            ], 404);
        }

        // Simpan password hash (apakah sudah ter-bcrypt atau raw text)
        if (str_starts_with($newPassword, '$2y$') || str_starts_with($newPassword, '$2a$') || str_starts_with($newPassword, '$2b$')) {
            $user->password = $newPassword;
        } else {
            $user->password = Hash::make($newPassword);
        }

        $user->is_default_password = false;
        $user->save();

        return response()->json([
            'status'   => true,
            'message'  => 'Password pengguna berhasil disinkronkan dari SiPintu.',
            'nis_nip'  => $user->nis_nip,
            'username' => $user->username,
        ]);
    }
}
