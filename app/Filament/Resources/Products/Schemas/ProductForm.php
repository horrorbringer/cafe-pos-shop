<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Product Details')
                    ->icon(Heroicon::OutlinedCube)
                    ->columns(2)
                    ->schema([
                        FileUpload::make('image')
                            ->label('Photo')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('products')
                            ->maxSize(2048)
                            ->columnSpanFull(),

                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state)))
                            ->columnSpan(1),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->disabled()
                            ->columnSpan(1),

                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->columnSpan(1),

                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$')
                            ->columnSpan(1),

                        Textarea::make('description')
                            ->rows(3)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ]),

                Section::make('Tags & Labels')
                    ->icon(Heroicon::OutlinedTag)
                    ->columns(2)
                    ->schema([
                        Select::make('tags')
                            ->label('Tags')
                            ->multiple()
                            ->options([
                                'new' => '🆕 New',
                                'popular' => '🔥 Popular',
                                'spicy' => '🌶️ Spicy',
                                'sweet' => '🍬 Sweet',
                                'vegan' => '🌱 Vegan',
                                'gluten_free' => '🌾 Gluten Free',
                                'seasonal' => '🍂 Seasonal',
                                'limited' => '⏳ Limited',
                                'signature' => '⭐ Signature',
                                'healthy' => '🥗 Healthy',
                            ])
                            ->native(false)
                            ->columnSpan(1),

                        TextInput::make('calories')
                            ->label('Calories (kcal)')
                            ->numeric()
                            ->minValue(0)
                            ->suffix('kcal')
                            ->placeholder('e.g. 250')
                            ->columnSpan(1),

                        Select::make('allergens')
                            ->label('Allergens')
                            ->multiple()
                            ->options([
                                'nuts' => '🥜 Nuts',
                                'dairy' => '🥛 Dairy',
                                'gluten' => '🌾 Gluten',
                                'eggs' => '🥚 Eggs',
                                'soy' => '🫘 Soy',
                                'seafood' => '🦐 Seafood',
                                'sesame' => '🌰 Sesame',
                            ])
                            ->native(false)
                            ->columnSpanFull(),
                    ]),

                Section::make('Inventory')
                    ->icon(Heroicon::OutlinedArchiveBox)
                    ->columns(2)
                    ->schema([
                        TextInput::make('stock_quantity')
                            ->label('Stock Quantity')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->suffix('units'),

                        Checkbox::make('is_available')
                            ->label('Available for sale')
                            ->default(true)
                            ->helperText('Uncheck to hide from the menu and POS.'),
                    ]),

                Section::make('Variants')
                    ->icon(Heroicon::OutlinedQueueList)
                    ->description('Size options (Small, Medium, Large) with price adjustments.')
                    ->collapsible()
                    ->schema([
                        Repeater::make('variants')
                            ->relationship()
                            ->schema([
                                Grid::make(4)->schema([
                                    TextInput::make('name')
                                        ->required()
                                        ->maxLength(255)
                                        ->placeholder('e.g. Small'),

                                    TextInput::make('price_adjustment')
                                        ->numeric()
                                        ->default(0)
                                        ->prefix('$')
                                        ->helperText('Added to base price'),

                                    TextInput::make('sort_order')
                                        ->numeric()
                                        ->default(0)
                                        ->minValue(0),

                                    Checkbox::make('is_active')
                                        ->label('Active')
                                        ->default(true),
                                ]),
                            ])
                            ->defaultItems(0)
                            ->collapsible()
                            ->cloneable()
                            ->addActionLabel('Add Variant')
                            ->itemLabel(fn (array $state): string => $state['name'] ?? 'New Variant'),
                    ]),

                Section::make('Modifier Groups')
                    ->icon(Heroicon::OutlinedAdjustmentsHorizontal)
                    ->description('Extra options like Sugar Level, Ice Level, or Toppings.')
                    ->collapsible()
                    ->schema([
                        Select::make('modifier_groups')
                            ->relationship(
                                name: 'modifierGroups',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query) => $query->orderBy('modifier_groups.sort_order'),
                            )
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->placeholder('Select modifier groups...'),
                    ]),

            ]);
    }
}
