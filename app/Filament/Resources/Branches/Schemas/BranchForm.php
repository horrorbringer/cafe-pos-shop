<?php

namespace App\Filament\Resources\Branches\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BranchForm
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

                            TextInput::make('phone')
                                ->maxLength(255),

                            Textarea::make('address')
                                ->rows(3)
                                ->maxLength(500),

                            Checkbox::make('is_active')
                                ->default(true),
                        ]),
                    ]),
            ]);
    }
}
