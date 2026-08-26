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
            ['name' => 'Makanan', 'icon' => 'fa-solid fa-utensils', 'description' => 'Makanan ringan dan berat'],
            ['name' => 'Minuman', 'icon' => 'fa-solid fa-mug-hot', 'description' => 'Minuman dingin dan hangat'],
            ['name' => 'Alat Tulis', 'icon' => 'fa-solid fa-pen-ruler', 'description' => 'ATK sekolah dan perlengkapan tulis'],
            ['name' => 'Aksesoris', 'icon' => 'fa-solid fa-gem', 'description' => 'Aksesoris dan pernak-pernik sekolah'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['name' => $cat['name']], $cat);
        }
    }
}