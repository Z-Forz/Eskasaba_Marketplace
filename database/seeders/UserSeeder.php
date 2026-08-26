<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            // ── Siswa ──────────────────────────────────────────────────────
            [
                'username'             => 'Budi Santoso',
                'nis_nip'              => '12345678',
                'email'                => 'budi@smkn1bangsri.sch.id',
                'password'             => Hash::make('password'),
                'is_default_password' => false,
                'role'                 => 'student',
                'api_id'               => 1,
                'phone'                => '081234567890',
            ],
            [
                'username'             => 'Siti Aisyah',
                'nis_nip'              => '12345679',
                'email'                => 'siti@smkn1bangsri.sch.id',
                'password'             => Hash::make('password'),
                'is_default_password' => false,
                'role'                 => 'student',
                'api_id'               => 2,
                'phone'                => '082345678901',
            ],
            [
                'username'             => 'Rizky Pratama',
                'nis_nip'              => '12345680',
                'email'                => 'rizky@smkn1bangsri.sch.id',
                'password'             => Hash::make('password'),
                'is_default_password' => false,
                'role'                 => 'student',
                'api_id'               => 3,
                'phone'                => null,
            ],

            // ── Guru ───────────────────────────────────────────────────────
            [
                'username'             => 'Ahmad Fauzi',
                'nis_nip'              => '198501012010011001',
                'email'                => 'ahmad@smkn1bangsri.sch.id',
                'password'             => Hash::make('password'),
                'is_default_password' => false,
                'role'                 => 'teacher',
                'api_id'               => 4,
                'phone'                => '085678901234',
            ],
            [
                'username'             => 'Dewi Rahayu',
                'nis_nip'              => '199002152015042002',
                'email'                => 'dewi@smkn1bangsri.sch.id',
                'password'             => Hash::make('password'),
                'is_default_password' => false,
                'role'                 => 'teacher',
                'api_id'               => 5,
                'phone'                => '086789012345',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(['nis_nip' => $userData['nis_nip']], $userData);
        }
    }
}