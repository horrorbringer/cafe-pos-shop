<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Domain\Shared\Enums\OrderStatus;
use App\Domain\Shared\Enums\OrderType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Order Info')
                    ->icon(Heroicon::OutlinedReceiptPercent)
                    ->columns(3)
                    ->schema([
                        TextInput::make('order_number')
                            ->label('Order #')
                            ->disabled()
                            ->dehydrated()
                            ->columnSpan(1),

                        Select::make('order_type')
                            ->label('Type')
                            ->options(collect(OrderType::cases())->mapWithKeys(
                                fn ($case) => [$case->value => $case->label()]
                            ))
                            ->default('dine_in')
                            ->required()
                            ->native(false),

                        Select::make('status')
                            ->label('Status')
                            ->options(collect(OrderStatus::cases())->mapWithKeys(
                                fn ($case) => [$case->value => $case->label()]
                            ))
                            ->default('pending')
                            ->required()
                            ->native(false),

                        TextInput::make('table_number')
                            ->label('Table #')
                            ->placeholder('--')
                            ->numeric(),
                    ]),

                Section::make('Payment')
                    ->icon(Heroicon::OutlinedCurrencyDollar)
                    ->columns(3)
                    ->schema([
                        TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->disabled(),

                        TextInput::make('discount')
                            ->label('Discount')
                            ->numeric()
                            ->prefix('$')
                            ->default(0),

                        TextInput::make('tax')
                            ->label('Tax')
                            ->numeric()
                            ->prefix('$')
                            ->default(0),

                        TextInput::make('total')
                            ->label('Total')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->disabled()
                            ->extraAttributes(['class' => 'font-semibold']),

                        TextInput::make('amount_paid')
                            ->label('Paid')
                            ->numeric()
                            ->prefix('$')
                            ->default(0),

                        TextInput::make('change_amount')
                            ->label('Change')
                            ->numeric()
                            ->prefix('$')
                            ->default(0)
                            ->disabled(),
                    ]),

                Section::make('Notes')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->schema([
                        Textarea::make('notes')
                            ->label('Order Notes')
                            ->rows(2)
                            ->maxLength(500)
                            ->placeholder('Any special instructions...'),
                    ]),

            ]);
    }
}
