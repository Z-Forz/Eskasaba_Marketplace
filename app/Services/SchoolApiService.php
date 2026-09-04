<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SchoolApiService — Layanan Integrasi SiPintu Identity & REST API Gateway.
 * Mendukung autentikasi Server-to-Server Gateway (Header Auth) dengan X-Client-ID & X-Client-Secret.
 */
class SchoolApiService
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;

    public function __construct()
    {
        $this->baseUrl      = rtrim(config('services.sipintu.url', 'https://sipintu.smkn1bangsri.sch.id'), '/');
        $this->clientId     = config('services.sipintu.client_id', 'app_2o8jtpekzdeh');
        $this->clientSecret = config('services.sipintu.client_secret', 'sec_BpEVnzLBIIP4eR4cdjhXHtdPF67Dj3OO');
    }

    /**
     * Heartbeat & Validasi Koneksi ke SiPintu Gateway.
     */
    /**
     * Heartbeat & Validasi Koneksi ke SiPintu Gateway.
     */
    public function ping(): bool
    {
        try {
            $response = Http::withoutVerifying()->timeout(5)->get("{$this->baseUrl}/api/v1/ping", [
                'client_id' => $this->clientId,
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Validasi NIS/NIP pengguna ke API Gateway SiPintu.
     *
     * @param  string  $nisNip  NIS (Siswa) atau NIP (Guru)
     * @return array<string, mixed>|null Data pengguna dari sekolah jika valid.
     */
    public function validate(string $nisNip): ?array
    {
        if (empty($nisNip)) {
            return null;
        }

        try {
            // First try SiPintu Server-to-Server Gateway Students & Teachers endpoints
            $response = Http::withoutVerifying()->timeout(8)
                ->withHeaders([
                    'X-Client-ID'     => $this->clientId,
                    'X-Client-Secret' => $this->clientSecret,
                    'Accept'          => 'application/json',
                ])
                ->get("{$this->baseUrl}/api/v1/sijuna/students", ['nis' => $nisNip]);

            if ($response->successful()) {
                $data = $response->json()['data'] ?? $response->json();
                $items = is_array($data) ? ($data[0] ?? $data) : $data;
                if (!empty($items['nis_nip']) || !empty($items['nis']) || !empty($items['id'])) {
                    return $this->formatUserData($items, 'student');
                }
            }

            // Try Teachers endpoint
            $responseTeacher = Http::withoutVerifying()->timeout(8)
                ->withHeaders([
                    'X-Client-ID'     => $this->clientId,
                    'X-Client-Secret' => $this->clientSecret,
                    'Accept'          => 'application/json',
                ])
                ->get("{$this->baseUrl}/api/v1/sijuna/teachers", ['nip' => $nisNip]);

            if ($responseTeacher->successful()) {
                $data = $responseTeacher->json()['data'] ?? $responseTeacher->json();
                $items = is_array($data) ? ($data[0] ?? $data) : $data;
                if (!empty($items['nis_nip']) || !empty($items['nip']) || !empty($items['id'])) {
                    return $this->formatUserData($items, 'teacher');
                }
            }
        } catch (\Exception $e) {
            Log::warning("SchoolApiService validate exception for {$nisNip}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Melakukan sinkronisasi seluruh data pengguna aktif dari SiPintu Gateway ke tabel users.
     *
     * @return int Jumlah pengguna yang berhasil disinkronkan
     */
    public function syncAllUsers(): int
    {
        $this->ping();

        $allUsersData = [];

        try {
            // Fetch Active Students from SiPintu Gateway Proxy
            $respStudents = Http::withoutVerifying()->timeout(20)
                ->withHeaders([
                    'X-Client-ID'     => $this->clientId,
                    'X-Client-Secret' => $this->clientSecret,
                    'Accept'          => 'application/json',
                ])
                ->get("{$this->baseUrl}/api/v1/sijuna/students");

            if ($respStudents->successful()) {
                $students = $respStudents->json()['data'] ?? $respStudents->json();
                if (is_array($students)) {
                    foreach ($students as $st) {
                        $st['role'] = 'student';
                        $allUsersData[] = $st;
                    }
                }
            } else {
                Log::warning("SiPintu Students HTTP status: " . $respStudents->status());
            }

            // Fetch Active Teachers from SiPintu Gateway Proxy
            $respTeachers = Http::withoutVerifying()->timeout(20)
                ->withHeaders([
                    'X-Client-ID'     => $this->clientId,
                    'X-Client-Secret' => $this->clientSecret,
                    'Accept'          => 'application/json',
                ])
                ->get("{$this->baseUrl}/api/v1/sijuna/teachers");

            if ($respTeachers->successful()) {
                $teachers = $respTeachers->json()['data'] ?? $respTeachers->json();
                if (is_array($teachers)) {
                    foreach ($teachers as $tc) {
                        $tc['role'] = 'teacher';
                        $allUsersData[] = $tc;
                    }
                }
            } else {
                Log::warning("SiPintu Teachers HTTP status: " . $respTeachers->status());
            }
        } catch (\Exception $e) {
            Log::error("SchoolApiService syncAllUsers API exception: " . $e->getMessage());
        }

        if (empty($allUsersData)) {
            Log::warning("SchoolApiService syncAllUsers: No user data returned from SiPintu Gateway.");
            return 0;
        }

        // Deduplicate array by NIS/NIP
        $uniqueUsers = [];
        foreach ($allUsersData as $item) {
            $rawNisNip = $item['nis_nip'] ?? $item['nis'] ?? $item['nip'] ?? null;
            if ($rawNisNip !== null && $rawNisNip !== '') {
                $uniqueUsers[(string) $rawNisNip] = $item;
            }
        }

        $now = now();
        $upsertData = [];

        foreach ($uniqueUsers as $nisNip => $item) {
            $role = match (strtolower($item['jenis_pengguna'] ?? $item['role'] ?? 'siswa')) {
                'guru', 'teacher' => 'teacher',
                default           => 'student',
            };

            $classRoom = null;
            if (isset($item['classroom']) && is_array($item['classroom'])) {
                $classRoom = $item['classroom']['name'] ?? $item['classroom']['nama'] ?? null;
            } elseif (isset($item['classroom']) && is_string($item['classroom'])) {
                $classRoom = $item['classroom'];
            } elseif (isset($item['kelas'])) {
                $classRoom = $item['kelas'];
            } elseif (isset($item['class_room'])) {
                $classRoom = $item['class_room'];
            }

            // Exclude alumni students (students without active X, XI, XII classroom)
            if ($role === 'student') {
                if (empty($classRoom) || !preg_match('/^(X|XI|XII)\s/i', trim($classRoom))) {
                    continue;
                }
            } else {
                if (empty($classRoom)) {
                    $classRoom = 'Dewan Guru';
                }
            }

            $rawEmail = $item['user']['email'] ?? $item['email'] ?? null;
            $email = $rawEmail;
            if (empty($email)) {
                $email = $nisNip . '@smkn1bangsri.sch.id';
            }

            $username = $item['nama'] ?? $item['name'] ?? $item['user']['name'] ?? $item['username'] ?? ('User ' . $nisNip);
            $phone = $item['hp'] ?? $item['telepon'] ?? $item['phone'] ?? null;

            $upsertData[] = [
                'nis_nip'             => (string) $nisNip,
                'username'            => (string) $username,
                'email'               => (string) $email,
                'role'                => (string) $role,
                'class_room'          => (string) $classRoom,
                'phone'               => $phone ? (string) $phone : null,
                'api_id'              => $item['id'] ?? null,
                'password'            => '$2y$12$mZc8nvSiP6snrKMPMkwmh.BsRQ/jaYv9Bc/IayudmIEOnQnGuS.9W',
                'is_default_password' => 1,
                'created_at'          => $now,
                'updated_at'          => $now,
            ];
        }

        // Deduplicate email within upsertData array to avoid email unique key constraint
        $uniqueEmails = [];
        $finalUpsert = [];
        $activeNisNips = [];

        foreach ($upsertData as $row) {
            $email = $row['email'];
            if (isset($uniqueEmails[$email])) {
                $row['email'] = $row['nis_nip'] . '@smkn1bangsri.sch.id';
            }
            $uniqueEmails[$row['email']] = true;
            $finalUpsert[] = $row;
            $activeNisNips[] = $row['nis_nip'];
        }

        $syncedCount = count($finalUpsert);

        // Perform chunked native upsert
        foreach (array_chunk($finalUpsert, 400) as $chunk) {
            User::upsert(
                $chunk,
                ['nis_nip'],
                ['username', 'email', 'role', 'class_room', 'phone', 'api_id', 'updated_at']
            );
        }

        // Purge alumni students from local database (students not present in active list)
        if (!empty($activeNisNips)) {
            User::where('role', 'student')
                ->whereNotIn('nis_nip', $activeNisNips)
                ->delete();
        }

        return $syncedCount;
    }

    protected function formatUserData(array $data, string $defaultRole): array
    {
        $nisNip = $data['nis_nip'] ?? $data['nis'] ?? $data['nip'] ?? null;
        $classRoom = null;
        if (isset($data['classroom']) && is_array($data['classroom'])) {
            $classRoom = $data['classroom']['name'] ?? $data['classroom']['nama'] ?? null;
        } elseif (isset($data['classroom']) && is_string($data['classroom'])) {
            $classRoom = $data['classroom'];
        } elseif (isset($data['kelas'])) {
            $classRoom = $data['kelas'];
        }

        return [
            'id'             => $data['id'] ?? null,
            'nis_nip'        => $nisNip,
            'nama'           => $data['nama'] ?? $data['name'] ?? $data['username'] ?? ('User ' . $nisNip),
            'jenis_pengguna' => strtolower($data['jenis_pengguna'] ?? $data['role'] ?? $defaultRole) === 'guru' ? 'guru' : 'siswa',
            'class_room'     => $classRoom,
            'telepon'        => $data['hp'] ?? $data['telepon'] ?? $data['phone'] ?? null,
            'email'          => $data['user']['email'] ?? $data['email'] ?? null,
        ];
    }
}
