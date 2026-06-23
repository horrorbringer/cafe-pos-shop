<?php

namespace App\Filament\Resources\Notifications\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NotificationRecipientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('channel_code')
                                ->relationship('channel', 'code')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->helperText('Select which channel this recipient belongs to.'),

                            TextInput::make('name')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('e.g. Manager, Kitchen Staff')
                                ->helperText('A label to identify this recipient.'),

                            TextInput::make('destination')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('e.g. admin@cafe.com or 123456789')
                                ->helperText('For Email channel: recipient email address. For Telegram: chat ID or username.'),

                            Toggle::make('is_active')
                                ->default(true)
                                ->helperText('Inactive recipients will not receive notifications.'),
                        ]),
                    ]),
            ]);
    }
}
