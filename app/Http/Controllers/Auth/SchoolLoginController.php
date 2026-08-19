<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Services\SchoolApiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SchoolLoginController extends Controller
{
    public function __construct(private SchoolApiService $schoolApi) {}

    /**
     * Display login page.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     * - Login pertama: validasi ke API Sekolah, buat akun lokal.
     * - Login berikutnya: auth lokal biasa tanpa hit API.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated(); // ['nis_nip' => ..., 'password' => ...]

        $localUser = User::where('nis_nip', $credentials['nis_nip'])->first();

        if ($localUser instanceof User) {
            // Sudah pernah login → auth lokal biasa, TIDAK hit API
            if (! Auth::attempt(['nis_nip' => $credentials['nis_nip'], 'password' => $credentials['password']])) {
                throw ValidationException::withMessages([
                    'nis_nip' => 'NIS/NIP atau password salah.',
                ]);
            }

            /** @var User $localUser */
            $localUser = Auth::user();
        } else {
            // Login pertama kali → validasi identitas ke API Sekolah
            $apiData = $this->schoolApi->validate($credentials['nis_nip']);

            if (! $apiData) {
                throw ValidationException::withMessages([
                    'nis_nip' => 'NIS/NIP tidak ditemukan di sistem sekolah.',
                ]);
            }

            /** @var array<string, mixed> $apiData */

            // Password default = 'password'
            if ($credentials['password'] !== 'password') {
                throw ValidationException::withMessages([
                    'nis_nip' => 'NIS/NIP atau password salah.',
                ]);
            }

            // Mapping role dari API ke role lokal
            $role = match ($apiData['jenis_pengguna'] ?? 'student') {
                'guru'  => 'teacher',
                default => 'student',
            };

            // Buat akun lokal dengan data dari API
            $localUser = User::create([
                'username'            => $apiData['nama'],
                'nis_nip'             => $apiData['nis_nip'],
                'role'                => $role,
                'password'            => Hash::make('password'),
                'is_default_password' => true,
                // Data profil sekolah
                'api_id'              => $apiData['id'] ?? null,
                'birth_date'          => $apiData['tanggal_lahir'] ?? null,
                'class'               => $apiData['kelas'] ?? null,
                'major'               => $apiData['jurusan'] ?? null,
                'phone'               => $apiData['telepon'] ?? null,
            ]);

            Auth::login($localUser);
        }

        $request->session()->regenerate();

        // Catat log login
        \App\Models\ActivityLog::record(
            $localUser->id,
            'login',
            "Login berhasil dari IP {$request->ip()}",
            $request
        );

        // Paksa ganti password default sebelum bisa akses fitur
        if ($localUser->is_default_password) {
            return redirect()->route('password.change')
                ->with('warning', 'Silakan ganti password default Anda terlebih dahulu.');
        }

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}