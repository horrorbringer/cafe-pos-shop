<?php

namespace Database\Seeders;

use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\ModifierGroup;
use App\Domain\Catalog\Models\Product;
use App\Domain\Shop\Models\Branch;
use Illuminate\Database\Seeder;
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

        $img = fn (string $name): string => 'https://placehold.co/400x400/amber/white?text='.urlencode($name);

        $products = [
            ['name' => 'Americano', 'price' => 4.50, 'category' => $coffee, 'calories' => 15, 'description' => 'Espresso shots topped with hot water', 'image' => $img('Americano')],
            ['name' => 'Latte', 'price' => 5.50, 'category' => $coffee, 'calories' => 190, 'description' => 'Espresso with steamed milk', 'image' => $img('Latte')],
            ['name' => 'Cappuccino', 'price' => 5.00, 'category' => $coffee, 'calories' => 150, 'description' => 'Espresso with steamed milk foam', 'image' => $img('Cappuccino')],
            ['name' => 'Espresso', 'price' => 3.50, 'category' => $coffee, 'calories' => 5, 'description' => 'Rich concentrated coffee', 'image' => $img('Espresso')],
            ['name' => 'Mocha', 'price' => 6.00, 'category' => $coffee, 'calories' => 250, 'description' => 'Espresso with chocolate and milk', 'image' => $img('Mocha')],
            ['name' => 'Green Tea', 'price' => 3.50, 'category' => $tea, 'calories' => 0, 'description' => 'Premium Japanese green tea', 'image' => $img('Green+Tea')],
            ['name' => 'Earl Grey', 'price' => 3.50, 'category' => $tea, 'calories' => 0, 'description' => 'Classic bergamot black tea', 'image' => $img('Earl+Grey')],
            ['name' => 'Chai Latte', 'price' => 5.00, 'category' => $tea, 'calories' => 180, 'description' => 'Spiced tea with steamed milk', 'image' => $img('Chai+Latte')],
            ['name' => 'Croissant', 'price' => 3.50, 'category' => $pastry, 'calories' => 230, 'description' => 'Flaky butter croissant', 'image' => $img('Croissant')],
            ['name' => 'Blueberry Muffin', 'price' => 3.00, 'category' => $pastry, 'calories' => 300, 'description' => 'Fresh baked blueberry muffin', 'image' => $img('Blueberry+Muffin')],
            ['name' => 'Chocolate Cake', 'price' => 4.50, 'category' => $pastry, 'calories' => 400, 'description' => 'Rich chocolate layer cake', 'image' => $img('Chocolate+Cake')],
            ['name' => 'Berry Blast', 'price' => 6.50, 'category' => $smoothies, 'calories' => 200, 'description' => 'Mixed berry and yogurt smoothie', 'image' => $img('Berry+Blast')],
            ['name' => 'Tropical Mango', 'price' => 6.00, 'category' => $smoothies, 'calories' => 220, 'description' => 'Mango and pineapple smoothie', 'image' => $img('Tropical+Mango')],
        ];

        foreach ($products as $data) {
            $category = $data['category'];
            unset($data['category']);

            $product = Product::firstOrCreate(
                ['slug' => Str::slug($data['name']), 'branch_id' => $branch->id],
                array_merge($data, [
                    'slug' => Str::slug($data['name']),
                    'branch_id' => $branch->id,
                    'category_id' => $category->id,
                    'stock_quantity' => 100,
                    'is_available' => true,
                ]),
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
