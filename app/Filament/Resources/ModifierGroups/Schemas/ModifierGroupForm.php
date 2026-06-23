<?php

namespace App\Filament\Resources\ModifierGroups\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ModifierGroupForm
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

                            TextInput::make('max_selections')
                                ->numeric()
                                ->default(1)
                                ->minValue(1)
                                ->required(),

                            Checkbox::make('is_required')
                                ->default(false),

                            Checkbox::make('is_active')
                                ->default(true),

                            TextInput::make('sort_order')
                                ->numeric()
                                ->default(0)
                                ->minValue(0),
                        ]),
                    ]),

                Section::make('Modifier Options')
                    ->schema([
                        Repeater::make('options')
                            ->relationship()
                            ->schema([
                                Grid::make(3)->schema([
                                    TextInput::make('name')
                                        ->required()
                                        ->maxLength(255),

                                    TextInput::make('price')
                                        ->numeric()
                                        ->default(0)
                                        ->minValue(0)
                                        ->prefix(config('pos.currency_symbol', '$')),

                                    TextInput::make('sort_order')
                                        ->numeric()
                                        ->default(0)
                                        ->minValue(0),

                                    Checkbox::make('is_active')
                                        ->default(true),
                                ]),
                            ])
                            ->defaultItems(1)
                            ->collapsible()
                            ->cloneable(),
                    ]),
            ]);
    }
}
