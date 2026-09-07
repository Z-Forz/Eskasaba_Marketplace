<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SiPintuService
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;

    public function __construct()
    {
        $this->baseUrl      = rtrim(env('SIPINTU_BASE_URL', config('services.sipintu.base_url', config('services.sipintu.url', 'https://sipintu.smkn1bangsri.sch.id'))), '/');
        $this->clientId     = env('SIPINTU_CLIENT_ID', config('services.sipintu.client_id', ''));
        $this->clientSecret = env('SIPINTU_CLIENT_SECRET', config('services.sipintu.client_secret', ''));
    }

    /**
     * HTTP Client dasar dengan autentikasi header SiPintu Gateway
     */
    protected function client()
    {
        return Http::withHeaders([
            'X-Client-ID'     => $this->clientId,
            'X-Client-Secret' => $this->clientSecret,
            'Accept'          => 'application/json',
        ])->timeout(10);
    }

    /**
     * Ping / Heartbeat ke SiPintu Gateway
     */
    public function ping(): array
    {
        try {
            $res = Http::acceptJson()->timeout(5)->get("{$this->baseUrl}/api/v1/ping", [
                'client_id' => $this->clientId,
            ]);

            return $res->json() ?? ['status' => 'offline'];
        } catch (\Exception $e) {
            return ['status' => 'offline', 'error' => $e->getMessage()];
        }
    }

    /**
     * Ambil data Siswa SIJUNA (Bisa filter NIS atau nama)
     */
    public function getStudents(?string $nis = null, ?string $search = null, int $limit = 50): array
    {
        $query = array_filter([
            'nis'    => $nis,
            'search' => $search,
            'limit'  => $limit,
        ]);

        try {
            $res = $this->client()->get("{$this->baseUrl}/api/v1/sijuna/students", $query);
            return $res->json('data') ?? ($res->json() ?? []);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Ambil data Guru SIJUNA (Bisa filter NIP atau nama)
     */
    public function getTeachers(?string $nip = null, ?string $search = null): array
    {
        $query = array_filter([
            'nip'    => $nip,
            'search' => $search,
        ]);

        try {
            $res = $this->client()->get("{$this->baseUrl}/api/v1/sijuna/teachers", $query);
            return $res->json('data') ?? ($res->json() ?? []);
        } catch (\Exception $e) {
            return [];
        }
    }
}
