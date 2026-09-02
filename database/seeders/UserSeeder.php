<?php

namespace Database\Seeders;

use App\Services\SchoolApiService;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Mensinkronkan seluruh data pengguna aktif (Siswa Kelas 10, 11, 12 & Guru) langsung dari SiPintu Gateway.
     */
    public function run(): void
    {
        $schoolApi = app(SchoolApiService::class);
        $schoolApi->syncAllUsers();
    }
}