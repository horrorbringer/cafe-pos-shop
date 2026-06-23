<?php

namespace App\Filament\Resources\Notifications\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class NotificationLogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event_code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('channel_code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('recipient')
                    ->searchable(),

                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('error_message')
                    ->limit(50),

                TextColumn::make('sent_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'queued' => 'Queued',
                        'sent' => 'Sent',
                        'failed' => 'Failed',
                    ]),

                SelectFilter::make('channel_code')
                    ->options([
                        'telegram' => 'Telegram',
                        'email' => 'Email',
                        'sms' => 'SMS',
                    ]),

                SelectFilter::make('event_code'),
            ]);
    }
}
