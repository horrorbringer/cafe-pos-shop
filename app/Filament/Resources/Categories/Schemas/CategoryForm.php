<?php

namespace App\Filament\Resources\Categories\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category Info')
                    ->icon(Heroicon::OutlinedRectangleStack)
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Category Name')
                            ->required()
                            ->maxLength(255)
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state)))
                            ->placeholder('e.g. Coffee, Pastries, Smoothies'),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->disabled(),

                        Select::make('icon')
                            ->label('Icon')
                            ->placeholder('Select an icon')
                            ->options([
                                '☕' => 'Coffee',
                                '🍵' => 'Tea',
                                '🥤' => 'Drink',
                                '🍽️' => 'Food',
                                '🍿' => 'Snack',
                                '🍰' => 'Dessert',
                                '🥐' => 'Pastry',
                                '🍞' => 'Bread',
                                '🍔' => 'Burger',
                                '🍕' => 'Pizza',
                                '🍜' => 'Noodle',
                                '🥗' => 'Salad',
                                '🍲' => 'Soup',
                                '🍦' => 'Ice Cream',
                                '🍓' => 'Fruit',
                                '🧃' => 'Juice',
                                '⭐' => 'Special',
                                '🔥' => 'Popular',
                                '📦' => 'Combo',
                                '➕' => 'Add-on',
                            ])
                            ->native(false),

                        TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->helperText('Lower numbers appear first.'),

                        Checkbox::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Inactive categories are hidden from the menu and POS.'),
                    ]),
            ]);
    }
}
