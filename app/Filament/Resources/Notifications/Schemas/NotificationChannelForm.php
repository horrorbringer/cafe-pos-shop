<?php

namespace App\Filament\Resources\Notifications\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class NotificationChannelForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Pick a Channel')
                    ->description('Choose how you want to send notifications.')
                    ->icon(Heroicon::OutlinedGlobeAlt)
                    ->columns(2)
                    ->schema([
                        Select::make('code')
                            ->label('Channel Type')
                            ->placeholder('Select...')
                            ->options([
                                'email' => 'Email',
                                'telegram' => 'Telegram',
                            ])
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabled(fn ($operation) => $operation === 'edit')
                            ->dehydrated()
                            ->live(),

                        Toggle::make('is_enabled')
                            ->label('Active')
                            ->inline(false)
                            ->default(true),
                    ]),

                Section::make('Email Settings')
                    ->icon(Heroicon::OutlinedEnvelope)
                    ->description('Configure your email sender details.')
                    ->columns(2)
                    ->visible(fn ($get) => $get('code') === 'email')
                    ->schema([
                        TextInput::make('settings.from_name')
                            ->label('Sender Name')
                            ->placeholder('Your Cafe'),

                        TextInput::make('settings.from_address')
                            ->label('Sender Email')
                            ->placeholder('noreply@yourcafe.com')
                            ->email(),

                        TextInput::make('settings.smtp_host')
                            ->label('SMTP Host')
                            ->placeholder('smtp.gmail.com'),

                        TextInput::make('settings.smtp_port')
                            ->label('SMTP Port')
                            ->placeholder('587')
                            ->numeric()
                            ->default(587),

                        TextInput::make('settings.smtp_username')
                            ->label('SMTP Username')
                            ->placeholder('your@email.com'),

                        TextInput::make('settings.smtp_password')
                            ->label('SMTP Password')
                            ->password()
                            ->placeholder('Enter password'),
                    ]),

                Section::make('Telegram Settings')
                    ->icon(Heroicon::OutlinedPaperAirplane)
                    ->description('Connect your Telegram bot.')
                    ->columns(2)
                    ->visible(fn ($get) => $get('code') === 'telegram')
                    ->schema([
                        TextInput::make('settings.bot_token')
                            ->label('Bot Token')
                            ->placeholder('1234567890:ABCdefGHIjklMNOpqrsTUVwxyz')
                            ->password()
                            ->revealable()
                            ->columnSpanFull()
                            ->helperText('Create a bot at @BotFather on Telegram, then paste the token.'),

                        TextInput::make('settings.chat_id')
                            ->label('Test Chat ID (optional)')
                            ->placeholder('-1001234567890'),
                    ]),

            ]);
    }
}
