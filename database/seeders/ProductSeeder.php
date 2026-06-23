<?php

namespace Database\Seeders;

use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\ModifierGroup;
use App\Domain\Catalog\Models\Product;
use App\Domain\Shop\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::where('name', 'Main Street Cafe')->firstOrFail();
        $modifierGroups = ModifierGroup::pluck('id', 'name');

        $coffee = Category::where('slug', 'coffee')->firstOrFail();
        $tea = Category::where('slug', 'tea')->firstOrFail();
        $pastry = Category::where('slug', 'pastry')->firstOrFail();
        $smoothies = Category::where('slug', 'smoothies')->firstOrFail();

        $beverageModifiers = ['Size', 'Sugar', 'Ice', 'Extras'];
        $smoothieModifiers = ['Size', 'Extras'];

        $unsplashPhotos = [
            'Americano' => '1509042239860-f550ce710b93',
            'Latte' => '1570968915860-54d5c301fa9f',
            'Cappuccino' => '1572442388796-11668a67e53d',
            'Espresso' => '1559496417-e7f25cb247f3',
            'Mocha' => '1578314675249-a6910f80cc4e',
            'Green Tea' => '1556881286-fc6915169721',
            'Earl Grey' => '1564890369478-c89ca6d9cde9',
            'Chai Latte' => '1563822249366-3efb23b8e0c9',
            'Croissant' => '1555507036-ab1f4038027a',
            'Blueberry Muffin' => '1557958118-97cee8645095',
            'Chocolate Cake' => '1578985545062-69928b1d9587',
            'Berry Blast' => '1505252585461-04db1eb84625',
            'Tropical Mango' => '1534353473418-4cfa6c56fd38',
        ];

        $products = [
            ['name' => 'Americano', 'price' => 4.50, 'category' => $coffee, 'calories' => 15, 'description' => 'Espresso shots topped with hot water', 'color' => '#8B4513'],
            ['name' => 'Latte', 'price' => 5.50, 'category' => $coffee, 'calories' => 190, 'description' => 'Espresso with steamed milk', 'color' => '#A0522D'],
            ['name' => 'Cappuccino', 'price' => 5.00, 'category' => $coffee, 'calories' => 150, 'description' => 'Espresso with steamed milk foam', 'color' => '#CD853F'],
            ['name' => 'Espresso', 'price' => 3.50, 'category' => $coffee, 'calories' => 5, 'description' => 'Rich concentrated coffee', 'color' => '#3E2723'],
            ['name' => 'Mocha', 'price' => 6.00, 'category' => $coffee, 'calories' => 250, 'description' => 'Espresso with chocolate and milk', 'color' => '#4E342E'],
            ['name' => 'Green Tea', 'price' => 3.50, 'category' => $tea, 'calories' => 0, 'description' => 'Premium Japanese green tea', 'color' => '#2E7D32'],
            ['name' => 'Earl Grey', 'price' => 3.50, 'category' => $tea, 'calories' => 0, 'description' => 'Classic bergamot black tea', 'color' => '#558B2F'],
            ['name' => 'Chai Latte', 'price' => 5.00, 'category' => $tea, 'calories' => 180, 'description' => 'Spiced tea with steamed milk', 'color' => '#BF8F4B'],
            ['name' => 'Croissant', 'price' => 3.50, 'category' => $pastry, 'calories' => 230, 'description' => 'Flaky butter croissant', 'color' => '#D4A373'],
            ['name' => 'Blueberry Muffin', 'price' => 3.00, 'category' => $pastry, 'calories' => 300, 'description' => 'Fresh baked blueberry muffin', 'color' => '#6B3FA0'],
            ['name' => 'Chocolate Cake', 'price' => 4.50, 'category' => $pastry, 'calories' => 400, 'description' => 'Rich chocolate layer cake', 'color' => '#5D4037'],
            ['name' => 'Berry Blast', 'price' => 6.50, 'category' => $smoothies, 'calories' => 200, 'description' => 'Mixed berry and yogurt smoothie', 'color' => '#C2185B'],
            ['name' => 'Tropical Mango', 'price' => 6.00, 'category' => $smoothies, 'calories' => 220, 'description' => 'Mango and pineapple smoothie', 'color' => '#FF8F00'],
        ];

        foreach ($products as $data) {
            $category = $data['category'];

            $slug = Str::slug($data['name']);
            $filename = $slug.'.jpg';
            $path = 'products/'.$filename;

            $photoId = $unsplashPhotos[$data['name']] ?? null;
            if ($photoId && ! Storage::disk('public')->exists($path)) {
                try {
                    $response = Http::timeout(10)
                        ->get("https://images.unsplash.com/photo-{$photoId}?w=400&q=80&fm=jpg&fit=crop");

                    if ($response->successful()) {
                        Storage::disk('public')->put($path, $response->body());
                    }
                } catch (\Exception $e) {
                    Log::warning('Could not download product image', [
                        'product' => $data['name'],
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            if (! Storage::disk('public')->exists($path)) {
                $filename = $slug.'.svg';
                $path = 'products/'.$filename;
                $letters = strtoupper(substr($data['name'], 0, 2));
                $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="400" height="400" viewBox="0 0 400 400">
  <rect width="400" height="400" fill="{$data['color']}"/>
  <text x="200" y="200" font-family="system-ui, sans-serif" font-size="120" fill="rgba(255,255,255,0.8)" text-anchor="middle" dominant-baseline="central" font-weight="bold">{$letters}</text>
</svg>
SVG;
                Storage::disk('public')->put($path, $svg);
            }

            $product = Product::updateOrCreate(
                ['slug' => $slug, 'branch_id' => $branch->id],
                [
                    'name' => $data['name'],
                    'slug' => $slug,
                    'branch_id' => $branch->id,
                    'category_id' => $category->id,
                    'price' => $data['price'],
                    'description' => $data['description'],
                    'calories' => $data['calories'],
                    'image' => $path,
                    'stock_quantity' => 100,
                    'is_available' => true,
                ],
            );

            $modifierKeys = match ($category->slug) {
                'smoothies' => $smoothieModifiers,
                'pastry' => [],
                default => $beverageModifiers,
            };

            $attachModifiers = [];
            foreach ($modifierKeys as $key) {
                if (isset($modifierGroups[$key])) {
                    $attachModifiers[] = $modifierGroups[$key];
                }
            }

            if ($attachModifiers !== []) {
                $product->modifierGroups()->syncWithoutDetaching($attachModifiers);
            }
        }

    }
}
