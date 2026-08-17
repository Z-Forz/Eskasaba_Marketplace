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
        $now = now();

        $users = [
            // ── Siswa ──────────────────────────────────────────────────────
            [
                'username'             => 'Budi Santoso',
                'nis_nip'              => '12345678',
                'email'                => null,
                'password'             => Hash::make('password'),
                'is_default_password' => false,
                'role'                 => 'student',
                'api_id'               => 1,
                'birth_date'           => '2008-05-12',
                'class'                => 'XII RPL 1',
                'major'                => 'Rekayasa Perangkat Lunak',
                'phone'                => '081234567890',
            ],
            [
                'username'             => 'Siti Aisyah',
                'nis_nip'              => '12345679',
                'email'                => null,
                'password'             => Hash::make('password'),
                'is_default_password' => false,
                'role'                 => 'student',
                'api_id'               => 2,
                'birth_date'           => '2009-03-22',
                'class'                => 'XI AKL 2',
                'major'                => 'Akuntansi dan Keuangan Lembaga',
                'phone'                => '082345678901',
            ],
            [
                'username'             => 'Rizky Pratama',
                'nis_nip'              => '12345680',
                'email'                => null,
                'password'             => Hash::make('password'),
                'is_default_password' => false,
                'role'                 => 'student',
                'api_id'               => 3,
                'birth_date'           => '2009-11-07',
                'class'                => 'XI RPL 2',
                'major'                => 'Rekayasa Perangkat Lunak',
                'phone'                => null,
            ],

            // ── Guru ───────────────────────────────────────────────────────
            [
                'username'             => 'Ahmad Fauzi',
                'nis_nip'              => '198501012010011001',
                'email'                => null,
                'password'             => Hash::make('password'),
                'is_default_password' => false,
                'role'                 => 'teacher',
                'api_id'               => 4,
                'birth_date'           => '1985-01-01',
                'class'                => null,
                'major'                => null,
                'subject_taught'       => 'Pemrograman Web & Bergerak',
                'phone'                => '085678901234',
            ],
            [
                'username'             => 'Dewi Rahayu',
                'nis_nip'              => '199002152015042002',
                'email'                => null,
                'password'             => Hash::make('password'),
                'is_default_password' => false,
                'role'                 => 'teacher',
                'api_id'               => 5,
                'birth_date'           => '1990-02-15',
                'class'                => null,
                'major'                => null,
                'subject_taught'       => 'Produk Kreatif & Kewirausahaan',
                'phone'                => '086789012345',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(['nis_nip' => $userData['nis_nip']], $userData);
        }
    }
}