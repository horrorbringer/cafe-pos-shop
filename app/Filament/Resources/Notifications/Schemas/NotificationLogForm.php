<?php

namespace App\Filament\Resources\Notifications\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NotificationLogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('event_code')
                            ->label('Event')
                            ->disabled(),

                        TextInput::make('channel_code')
                            ->label('Channel')
                            ->disabled(),

                        TextInput::make('recipient')
                            ->label('Recipient')
                            ->disabled(),

                        TextInput::make('status')
                            ->label('Status')
                            ->disabled(),

                        TextInput::make('sent_at')
                            ->label('Sent At')
                            ->disabled(),

                        TextInput::make('created_at')
                            ->label('Logged At')
                            ->disabled(),

                        Textarea::make('error_message')
                            ->label('Error')
                            ->rows(3)
                            ->disabled()
                            ->columnSpanFull(),

                        Textarea::make('payload')
                            ->label('Payload')
                            ->rows(5)
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
