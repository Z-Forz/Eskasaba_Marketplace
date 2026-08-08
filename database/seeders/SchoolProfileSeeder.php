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
                'birth_date'     => '1990-01-01',
                'class'          => null,
                'major'          => null,
                'phone'          => '081234567890',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'api_id'         => 'API002',
                'school_number'  => '22001',
                'name'           => 'Budi Santoso',
                'type'           => 'student',
                'birth_date'     => '2008-05-15',
                'class'          => 'XI',
                'major'          => 'PPLG',
                'phone'          => '081234567891',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'api_id'         => 'API003',
                'school_number'  => '22002',
                'name'           => 'Siti Aisyah',
                'type'           => 'student',
                'birth_date'     => '2008-07-20',
                'class'          => 'XI',
                'major'          => 'AKL',
                'phone'          => '081234567892',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ]);
    }
}