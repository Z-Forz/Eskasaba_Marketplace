<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::firstOrCreate(
            ['username' => 'admin'],
            [
                'email'    => 'admin@eskasaba.com',
                'password' => Hash::make('password123'),
            ]
        );
    }
}