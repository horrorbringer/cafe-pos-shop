<?php

namespace Database\Seeders;

use App\Domain\Inventory\Models\InventoryItem;
use App\Domain\Shop\Models\Branch;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::where('name', 'Main Street Cafe')->firstOrFail();

        $items = [
            ['name' => 'Coffee Beans', 'unit' => 'kg', 'quantity' => 25, 'minimum_quantity' => 5, 'cost_per_unit' => 15.00],
            ['name' => 'Milk', 'unit' => 'L', 'quantity' => 40, 'minimum_quantity' => 10, 'cost_per_unit' => 2.50],
            ['name' => 'Sugar', 'unit' => 'kg', 'quantity' => 15, 'minimum_quantity' => 3, 'cost_per_unit' => 1.80],
            ['name' => 'Ice Cubes', 'unit' => 'kg', 'quantity' => 30, 'minimum_quantity' => 5, 'cost_per_unit' => 0.50],
            ['name' => 'Tea Leaves', 'unit' => 'kg', 'quantity' => 10, 'minimum_quantity' => 2, 'cost_per_unit' => 20.00],
            ['name' => 'Flour', 'unit' => 'kg', 'quantity' => 20, 'minimum_quantity' => 5, 'cost_per_unit' => 1.20],
            ['name' => 'Butter', 'unit' => 'kg', 'quantity' => 10, 'minimum_quantity' => 2, 'cost_per_unit' => 4.50],
            ['name' => 'Mixed Berries', 'unit' => 'kg', 'quantity' => 8, 'minimum_quantity' => 2, 'cost_per_unit' => 8.00],
            ['name' => 'Mango Puree', 'unit' => 'L', 'quantity' => 10, 'minimum_quantity' => 2, 'cost_per_unit' => 6.00],
            ['name' => 'Chocolate Syrup', 'unit' => 'L', 'quantity' => 5, 'minimum_quantity' => 1, 'cost_per_unit' => 7.50],
        ];

        foreach ($items as $item) {
            InventoryItem::firstOrCreate(
                ['name' => $item['name'], 'branch_id' => $branch->id],
                array_merge($item, ['branch_id' => $branch->id]),
            );
        }
    }
}
