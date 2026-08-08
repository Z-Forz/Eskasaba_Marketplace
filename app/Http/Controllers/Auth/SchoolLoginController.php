<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Services\SchoolApiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SchoolLoginController extends Controller
{
    public function __construct(private SchoolApiService $schoolApi) {}

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated(); // ['nis_nip' => ..., 'password' => ...]

        $localUser = User::where('nis_nip', $credentials['nis_nip'])->first();

        if ($localUser) {
            // Sudah pernah login sebelumnya -> auth lokal biasa, TIDAK hit API
            if (! Auth::attempt($credentials)) {
                throw ValidationException::withMessages([
                    'nis_nip' => 'NIS/NIP atau password salah.',
                ]);
            }
            $localUser = Auth::user();
        } else {
            // Login pertama kali -> validasi identitas ke API Sekolah dulu
            $apiData = $this->schoolApi->validate($credentials['nis_nip']);

            if (! $apiData) {
                throw ValidationException::withMessages([
                    'nis_nip' => 'NIS/NIP tidak ditemukan.',
                ]);
            }

            if ($credentials['password'] !== 'password') {
                throw ValidationException::withMessages([
                    'nis_nip' => 'NIS/NIP atau password salah.',
                ]);
            }

            $localUser = User::create([
                'username'             => $apiData['nama'], // nama asli dari API, misal "Budi Santoso"
                'nis_nip'              => $apiData['nis_nip'],
                'role'                 => $apiData['jenis_pengguna'], // siswa/guru
                'password'             => Hash::make('password'),
                'is_default_password'  => true,
            ]);

            Auth::login($localUser);
        }

        $request->session()->regenerate(); // cegah session fixation

        if ($localUser->is_default_password) {
            return redirect()->route('password.change')
                ->with('warning', 'Silakan ganti password default Anda terlebih dahulu.');
        }

        return redirect()->intended(route('dashboard'));
    }
}