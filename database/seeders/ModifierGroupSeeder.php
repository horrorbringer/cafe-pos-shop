<?php

namespace Database\Seeders;

use App\Domain\Catalog\Models\ModifierGroup;
use Illuminate\Database\Seeder;

class ModifierGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            [
                'name' => 'Size',
                'is_required' => true,
                'max_selections' => 1,
                'sort_order' => 0,
                'options' => [
                    ['name' => 'Small', 'price' => -0.50, 'sort_order' => 0],
                    ['name' => 'Medium', 'price' => 0, 'sort_order' => 1],
                    ['name' => 'Large', 'price' => 0.50, 'sort_order' => 2],
                ],
            ],
            [
                'name' => 'Sugar',
                'is_required' => true,
                'max_selections' => 1,
                'sort_order' => 1,
                'options' => [
                    ['name' => 'No Sugar', 'price' => 0, 'sort_order' => 0],
                    ['name' => 'Less Sugar', 'price' => 0, 'sort_order' => 1],
                    ['name' => 'Regular Sugar', 'price' => 0, 'sort_order' => 2],
                    ['name' => 'Extra Sugar', 'price' => 0, 'sort_order' => 3],
                ],
            ],
            [
                'name' => 'Ice',
                'is_required' => true,
                'max_selections' => 1,
                'sort_order' => 2,
                'options' => [
                    ['name' => 'No Ice', 'price' => 0, 'sort_order' => 0],
                    ['name' => 'Less Ice', 'price' => 0, 'sort_order' => 1],
                    ['name' => 'Regular Ice', 'price' => 0, 'sort_order' => 2],
                    ['name' => 'Extra Ice', 'price' => 0, 'sort_order' => 3],
                ],
            ],
            [
                'name' => 'Extras',
                'is_required' => false,
                'max_selections' => 3,
                'sort_order' => 3,
                'options' => [
                    ['name' => 'Extra Shot', 'price' => 0.50, 'sort_order' => 0],
                    ['name' => 'Oat Milk', 'price' => 0.75, 'sort_order' => 1],
                    ['name' => 'Soy Milk', 'price' => 0.50, 'sort_order' => 2],
                    ['name' => 'Vanilla Syrup', 'price' => 0.30, 'sort_order' => 3],
                    ['name' => 'Caramel Syrup', 'price' => 0.30, 'sort_order' => 4],
                    ['name' => 'Hazelnut Syrup', 'price' => 0.30, 'sort_order' => 5],
                ],
            ],
        ];

        foreach ($groups as $groupData) {
            $options = $groupData['options'];
            unset($groupData['options']);

            $group = ModifierGroup::firstOrCreate(
                ['name' => $groupData['name']],
                $groupData,
            );

            foreach ($options as $optionData) {
                $group->options()->firstOrCreate(
                    ['name' => $optionData['name']],
                    $optionData,
                );
            }
        }
    }
}
