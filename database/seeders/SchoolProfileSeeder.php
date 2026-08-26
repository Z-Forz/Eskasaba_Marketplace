<?php

namespace Database\Seeders;

use App\Models\SchoolProfile;
use Illuminate\Database\Seeder;

class SchoolProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SchoolProfile::insert([
            [
                'api_id'         => 'API001',
                'school_number'  => '1234567890',
                'name'           => 'Administrator',
                'type'           => 'teacher',
                'phone'          => '081234567890',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'api_id'         => 'API002',
                'school_number'  => '22001',
                'name'           => 'Budi Santoso',
                'type'           => 'student',
                'phone'          => '081234567891',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'api_id'         => 'API003',
                'school_number'  => '22002',
                'name'           => 'Siti Aisyah',
                'type'           => 'student',
                'phone'          => '081234567892',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ]);
    }
}