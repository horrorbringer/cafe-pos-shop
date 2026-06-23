<?php

namespace App\Filament\Resources\Notifications\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NotificationChannelsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Channel')
                    ->searchable()
                    ->sortable()
                    ->icon(fn ($state) => match ($state) {
                        'email' => 'heroicon-o-envelope',
                        'telegram' => 'heroicon-o-paper-airplane',
                        default => 'heroicon-o-bell',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'email' => 'Email',
                        'telegram' => 'Telegram',
                        default => ucfirst($state),
                    }),

                IconColumn::make('is_enabled')
                    ->boolean()
                    ->label('Active'),

                TextColumn::make('recipients_count')
                    ->counts('recipients')
                    ->label('Recipients')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('rules_count')
                    ->counts('rules')
                    ->label('Rules')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('created_at')
                    ->label('Added')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('')
                    ->icon('heroicon-o-pencil-square'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
