<?php

namespace Database\Seeders;

use App\Domain\Catalog\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Coffee', 'slug' => 'coffee', 'icon' => '☕', 'sort_order' => 0, 'is_active' => true],
            ['name' => 'Tea', 'slug' => 'tea', 'icon' => '🍵', 'sort_order' => 1, 'is_active' => true],
            ['name' => 'Pastry', 'slug' => 'pastry', 'icon' => '🥐', 'sort_order' => 2, 'is_active' => true],
            ['name' => 'Smoothies', 'slug' => 'smoothies', 'icon' => '🥤', 'sort_order' => 3, 'is_active' => true],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
