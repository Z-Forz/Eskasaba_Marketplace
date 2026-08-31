<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SchoolApiService — Layanan Integrasi Database API Sekolah.
 * Melakukan validasi NIS/NIP dan sinkronisasi data pengguna dari API Sekolah.
 */
class SchoolApiService
{
    protected string $baseUrl;

    protected string $token;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.school_api.url', 'https://api.sekolah.example'), '/');
        $this->token   = config('services.school_api.token', '');
    }

    /**
     * Validasi NIS/NIP pengguna ke API Sekolah.
     *
     * @param  string  $nisNip  NIS (Siswa) atau NIP (Guru)
     * @return array<string, mixed>|null Data pengguna dari sekolah jika valid, null jika tidak ditemukan/gagal.
     */
    public function validate(string $nisNip): ?array
    {
        if (empty($nisNip)) {
            return null;
        }

        try {
            $request = Http::timeout(5);

            if (! empty($this->token)) {
                $request->withToken($this->token);
            }

            // Memanggil endpoint API Sekolah (GET /users/{nisNip})
            $response = $request->get("{$this->baseUrl}/users/{$nisNip}");

            if ($response->successful()) {
                $data = $response->json()['data'] ?? $response->json();

                if (! empty($data['nis_nip']) || ! empty($data['id'])) {
                    return [
                        'id'             => $data['id'] ?? null,
                        'nis_nip'        => $data['nis_nip'] ?? $nisNip,
                        'nama'           => $data['nama'] ?? $data['name'] ?? $data['username'] ?? ('User ' . $nisNip),
                        'jenis_pengguna' => strtolower($data['jenis_pengguna'] ?? $data['role'] ?? 'siswa') === 'guru' ? 'guru' : 'siswa',
                        'class_room'     => $data['kelas'] ?? $data['class_room'] ?? $data['class'] ?? null,
                        'telepon'        => $data['telepon'] ?? $data['phone'] ?? null,
                        'email'          => $data['email'] ?? null,
                    ];
                }
            }

            Log::warning("SchoolApiService validate failed for NIS/NIP {$nisNip}: HTTP {$response->status()}");

            return null;
        } catch (\Exception $e) {
            Log::error("SchoolApiService exception during validate ({$nisNip}): {$e->getMessage()}");

            return null;
        }
    }

    /**
     * Melakukan sinkronisasi seluruh data pengguna dari API Database Sekolah ke tabel users lokal.
     *
     * @return int Jumlah pengguna yang berhasil disinkronkan
     */
    public function syncAllUsers(): int
    {
        try {
            $request = Http::timeout(10);

            if (! empty($this->token)) {
                $request->withToken($this->token);
            }

            $response = $request->get("{$this->baseUrl}/users");

            if (! $response->successful()) {
                Log::error("SchoolApiService syncAllUsers failed: HTTP {$response->status()} - {$response->body()}");

                return 0;
            }

            $schoolUsers = $response->json()['data'] ?? $response->json();

            if (! is_array($schoolUsers)) {
                return 0;
            }

            $syncedCount = 0;

            foreach ($schoolUsers as $item) {
                $nisNip = $item['nis_nip'] ?? null;
                if (! $nisNip) {
                    continue;
                }

                $role = match (strtolower($item['jenis_pengguna'] ?? $item['role'] ?? 'siswa')) {
                    'guru', 'teacher' => 'teacher',
                    default           => 'student',
                };

                User::updateOrCreate(
                    ['nis_nip' => $nisNip],
                    [
                        'username'            => $item['nama'] ?? $item['name'] ?? $item['username'] ?? ('User ' . $nisNip),
                        'email'               => $item['email'] ?? ($nisNip . '@sekolah.id'),
                        'role'                => $role,
                        'class_room'           => $item['kelas'] ?? $item['class_room'] ?? $item['class'] ?? null,
                        'phone'               => $item['telepon'] ?? $item['phone'] ?? null,
                        'api_id'              => $item['id'] ?? null,
                        'password'            => Hash::make('password'),
                        'is_default_password' => true,
                    ]
                );

                $syncedCount++;
            }

            return $syncedCount;
        } catch (\Exception $e) {
            Log::error("SchoolApiService syncAllUsers exception: {$e->getMessage()}");

            return 0;
        }
    }
}
