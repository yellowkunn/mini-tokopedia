<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['icon' => '📱', 'name' => 'Handphone'],
            ['icon' => '💻', 'name' => 'Komputer'],
            ['icon' => '👕', 'name' => 'Fashion'],
            ['icon' => '🍔', 'name' => 'Makanan'],
            ['icon' => '🏠', 'name' => 'Rumah'],
            ['icon' => '🎮', 'name' => 'Gaming'],
            ['icon' => '📚', 'name' => 'Buku'],
            ['icon' => '⚽', 'name' => 'Olahraga'],
            ['icon' => '💄', 'name' => 'Kecantikan'],
            ['icon' => '🔧', 'name' => 'Otomotif'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name'  => $category['name'],
                'slug'  => Str::slug($category['name']),
                'image' => $category['icon'], // Menyimpan emoji ke kolom image
            ]);
        }
    }
}