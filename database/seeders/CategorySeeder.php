<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::insert([
            [
                'name' => 'Makanan',
                'description' => 'Makanan ringan dan berat',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Minuman',
                'description' => 'Minuman dingin dan hangat',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Alat Tulis',
                'description' => 'ATK sekolah',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Aksesoris',
                'description' => 'Aksesoris sekolah',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}