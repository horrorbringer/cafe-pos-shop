<?php

namespace App\Filament\Resources\InventoryItems\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InventoryItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255),

                            Select::make('branch_id')
                                ->relationship('branch', 'name')
                                ->required()
                                ->searchable()
                                ->preload(),

                            TextInput::make('unit')
                                ->required()
                                ->maxLength(50)
                                ->placeholder('e.g., kg, liters, pieces'),

                            TextInput::make('quantity')
                                ->numeric()
                                ->default(0)
                                ->minValue(0),

                            TextInput::make('minimum_quantity')
                                ->numeric()
                                ->default(0)
                                ->minValue(0)
                                ->label('Minimum Stock Level'),

                            TextInput::make('cost_per_unit')
                                ->numeric()
                                ->default(0)
                                ->minValue(0)
                                ->prefix('$'),
                        ]),
                    ]),
            ]);
    }
}
