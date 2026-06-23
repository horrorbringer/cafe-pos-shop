<?php

namespace Database\Seeders;

use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Models\ProductIngredient;
use App\Domain\Inventory\Models\InventoryItem;
use App\Domain\Shop\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductIngredientSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::where('name', 'Main Street Cafe')->firstOrFail();

        $inventory = fn (string $name) => InventoryItem::where('name', $name)
            ->where('branch_id', $branch->id)
            ->firstOrFail();

        $product = fn (string $name) => Product::where('slug', Str::slug($name))
            ->where('branch_id', $branch->id)
            ->firstOrFail();

        $ingredients = [
            'Americano' => [['inventoryItem' => $inventory('Coffee Beans'), 'quantity' => 0.02]],
            'Latte' => [['inventoryItem' => $inventory('Coffee Beans'), 'quantity' => 0.02], ['inventoryItem' => $inventory('Milk'), 'quantity' => 0.25]],
            'Cappuccino' => [['inventoryItem' => $inventory('Coffee Beans'), 'quantity' => 0.02], ['inventoryItem' => $inventory('Milk'), 'quantity' => 0.15]],
            'Espresso' => [['inventoryItem' => $inventory('Coffee Beans'), 'quantity' => 0.01]],
            'Mocha' => [['inventoryItem' => $inventory('Coffee Beans'), 'quantity' => 0.02], ['inventoryItem' => $inventory('Chocolate Syrup'), 'quantity' => 0.03], ['inventoryItem' => $inventory('Milk'), 'quantity' => 0.20]],
            'Green Tea' => [['inventoryItem' => $inventory('Tea Leaves'), 'quantity' => 0.01]],
            'Earl Grey' => [['inventoryItem' => $inventory('Tea Leaves'), 'quantity' => 0.01]],
            'Chai Latte' => [['inventoryItem' => $inventory('Tea Leaves'), 'quantity' => 0.01], ['inventoryItem' => $inventory('Milk'), 'quantity' => 0.25]],
            'Croissant' => [['inventoryItem' => $inventory('Flour'), 'quantity' => 0.10], ['inventoryItem' => $inventory('Butter'), 'quantity' => 0.05]],
            'Blueberry Muffin' => [['inventoryItem' => $inventory('Flour'), 'quantity' => 0.10], ['inventoryItem' => $inventory('Butter'), 'quantity' => 0.03], ['inventoryItem' => $inventory('Mixed Berries'), 'quantity' => 0.04]],
            'Chocolate Cake' => [['inventoryItem' => $inventory('Flour'), 'quantity' => 0.12], ['inventoryItem' => $inventory('Butter'), 'quantity' => 0.06], ['inventoryItem' => $inventory('Chocolate Syrup'), 'quantity' => 0.05]],
            'Berry Blast' => [['inventoryItem' => $inventory('Mixed Berries'), 'quantity' => 0.15]],
            'Tropical Mango' => [['inventoryItem' => $inventory('Mango Puree'), 'quantity' => 0.20]],
        ];

        foreach ($ingredients as $productName => $productIngredients) {
            $p = $product($productName);
            foreach ($productIngredients as $ingredient) {
                ProductIngredient::firstOrCreate([
                    'product_id' => $p->id,
                    'inventory_item_id' => $ingredient['inventoryItem']->id,
                ], [
                    'quantity_required' => $ingredient['quantity'],
                ]);
            }
        }
    }
}
