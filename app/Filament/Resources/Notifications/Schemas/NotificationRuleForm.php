<?php

namespace App\Filament\Resources\Notifications\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NotificationRuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('event_code')
                                ->options([
                                    'order_paid' => 'Order Paid',
                                    'payment_failed' => 'Payment Failed',
                                    'daily_sales_summary' => 'Daily Sales Summary',
                                    'low_stock' => 'Low Stock',
                                    'refund_void' => 'Refund / Void',
                                ])
                                ->required()
                                ->searchable(),

                            Select::make('channel_code')
                                ->relationship('channel', 'code')
                                ->required()
                                ->searchable()
                                ->preload(),

                            Toggle::make('is_enabled')
                                ->default(false),

                            TextInput::make('cooldown_minutes')
                                ->numeric()
                                ->default(60)
                                ->minValue(0)
                                ->required()
                                ->suffix('minutes')
                                ->helperText('Minimum wait time between notifications for this event. Set 0 to send every time.'),
                        ]),
                    ]),
            ]);
    }
}
