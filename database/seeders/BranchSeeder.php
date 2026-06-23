<?php

namespace Database\Seeders;

use App\Domain\Shop\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branch = Branch::firstOrCreate(
            ['name' => 'Main Street Cafe'],
            [
                'address' => '123 Main Street, Downtown',
                'phone' => '+1-555-0100',
                'is_active' => true,
            ],
        );

        $tables = [];
        for ($i = 1; $i <= 6; $i++) {
            $tables[] = ['number' => (string) $i, 'capacity' => 2, 'is_active' => true];
        }
        for ($i = 7; $i <= 9; $i++) {
            $tables[] = ['number' => (string) $i, 'capacity' => 4, 'is_active' => true];
        }
        $tables[] = ['number' => '10', 'capacity' => 6, 'is_active' => true];

        foreach ($tables as $table) {
            $branch->tables()->firstOrCreate(
                ['number' => $table['number']],
                $table,
            );
        }
    }
}
