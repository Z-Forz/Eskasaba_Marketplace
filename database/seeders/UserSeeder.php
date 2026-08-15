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
        User::insert([
            [
                'school_profile_id' => 1,
                'username'          => 'Admin',
                'name'              => 'Administrator',
                'email'             => 'admin@eskasaba.com',
                'password'          => Hash::make('password123'),
                'role'              => 'admin',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'school_profile_id' => 2,
                'name'              => 'Budi Santoso',
                'email'             => 'budi@eskasaba.com',
                'password'          => Hash::make('password'),
                'role'              => 'student',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
            [
                'school_profile_id' => 3,
                'name'              => 'Siti Aisyah',
                'email'             => 'siti@eskasaba.com',
                'password'          => Hash::make('password'),
                'role'              => 'student',
                'created_at'        => now(),
                'updated_at'        => now(),
            ],
        ]);
    }
}