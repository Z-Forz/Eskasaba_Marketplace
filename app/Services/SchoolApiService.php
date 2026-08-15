<?php

namespace App\Services;

/**
 * SchoolApiService — placeholder sampai integrasi API Sekolah nyata tersedia.
 * Saat ini mengembalikan data dummy agar alur login bisa berjalan di lokal.
 *
 * Ganti implementasi method validate() dengan HTTP call ke API Sekolah asli
 * ketika endpoint sudah tersedia.
 */
class SchoolApiService
{
    public function __construct()
    {
        // placeholder
    }

    /**
     * Return a list of school profiles (empty placeholder).
     *
     * @return array<int, array<string,mixed>>
     */
    public function fetchProfiles(): array
    {
        return [];
    }

    /**
     * Return a single profile by id or null if not found.
     *
     * @param  int  $id
     * @return array<string,mixed>|null
     */
    public function getProfile(int $id): ?array
    {
        return null;
    }

    /**
     * Validate user credentials against the school system.
     *
     * Returns an associative array with the user's school data on success,
     * or null if the NIS/NIP is not found in the school system.
     *
     * Expected keys in the returned array:
     *   - id              (int)     — ID unik dari sistem sekolah
     *   - nis_nip         (string)  — NIS (siswa) atau NIP (guru)
     *   - nama            (string)  — Nama lengkap
     *   - jenis_pengguna  (string)  — 'siswa' | 'guru'
     *   - tanggal_lahir   (string|null) — format Y-m-d
     *   - kelas           (string|null) — kelas (khusus siswa)
     *   - jurusan         (string|null) — jurusan (khusus siswa)
     *   - telepon         (string|null) — nomor HP
     *
     * TODO: Ganti implementasi ini dengan HTTP call ke API Sekolah asli.
     *
     * @param  string  $nisNip  NIS atau NIP yang diinput user
     * @return array<string,mixed>|null  Data sekolah jika ditemukan, null jika tidak
     */
    public function validate(string $nisNip): array|null
    {
        // ---------------------------------------------------------------
        // PLACEHOLDER — hapus blok ini dan ganti dengan panggilan API asli
        // ---------------------------------------------------------------
        // Contoh dummy: anggap semua NIS/NIP valid untuk keperluan development
        return [
            'id'             => 1,
            'nis_nip'        => $nisNip,
            'nama'           => 'User ' . $nisNip,
            'jenis_pengguna' => 'siswa',
            'tanggal_lahir'  => null,
            'kelas'          => null,
            'jurusan'        => null,
            'telepon'        => null,
        ];

        // ---------------------------------------------------------------
        // IMPLEMENTASI NYATA (contoh dengan HTTP):
        // ---------------------------------------------------------------
        // $response = Http::get('https://api.sekolah.example/users/' . $nisNip);
        // if ($response->failed()) return null;
        // return $response->json();
    }
}
