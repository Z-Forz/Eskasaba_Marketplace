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
        $categories = [
            ['name' => 'Makanan', 'description' => 'Makanan ringan dan berat'],
            ['name' => 'Minuman', 'description' => 'Minuman dingin dan hangat'],
            ['name' => 'Alat Tulis', 'description' => 'ATK sekolah'],
            ['name' => 'Aksesoris', 'description' => 'Aksesoris sekolah'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['name' => $cat['name']], $cat);
        }
    }
}