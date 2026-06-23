<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Domain\Shared\Enums\OrderStatus;
use App\Domain\Shared\Enums\OrderType;
use App\Domain\Shared\Enums\PaymentMethod;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('Order')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, H:i')
                    ->sortable()
                    ->description(fn ($record) => $record->created_at->diffForHumans()),

                TextColumn::make('order_type')
                    ->label('')
                    ->formatStateUsing(fn (OrderType $state): string => $state->label())
                    ->badge()
                    ->color(fn (OrderType $state): string => match ($state) {
                        OrderType::DineIn => 'info',
                        OrderType::Takeaway => 'warning',
                        OrderType::Delivery => 'success',
                    })
                    ->icon(fn (OrderType $state): string => match ($state) {
                        OrderType::DineIn => 'heroicon-m-building-storefront',
                        OrderType::Takeaway => 'heroicon-m-shopping-bag',
                        OrderType::Delivery => 'heroicon-m-truck',
                    }),

                TextColumn::make('table_number')
                    ->label('Table')
                    ->badge()
                    ->color('gray')
                    ->visible(fn ($state) => filled($state)),

                TextColumn::make('user.name')
                    ->label('Cashier')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Items')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('USD')
                    ->sortable()
                    ->weight('semibold'),

                IconColumn::make('payments.method')
                    ->label('Paid via')
                    ->icon(fn (?PaymentMethod $state) => match ($state) {
                        PaymentMethod::Cash => 'heroicon-o-banknotes',
                        PaymentMethod::Card => 'heroicon-o-credit-card',
                        PaymentMethod::Qr => 'heroicon-o-device-phone-mobile',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->state(fn ($record) => $record->payments->first()?->method)
                    ->toggleable(),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (OrderStatus $state): string => $state->label())
                    ->color(fn (OrderStatus $state): string => $state->color())
                    ->sortable()
                    ->icon(fn (OrderStatus $state): string => match ($state) {
                        OrderStatus::Completed => 'heroicon-m-check-circle',
                        OrderStatus::Paid => 'heroicon-m-credit-card',
                        OrderStatus::Pending => 'heroicon-m-clock',
                        OrderStatus::Cancelled => 'heroicon-m-x-circle',
                        OrderStatus::Refunded => 'heroicon-m-arrow-uturn-left',
                        default => 'heroicon-m-document',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'refunded' => 'Refunded',
                    ])
                    ->label('Status')
                    ->native(false),

                Filter::make('today')
                    ->query(fn (Builder $query): Builder => $query->whereDate('created_at', today()))
                    ->label('Today')
                    ->default(),

                SelectFilter::make('order_type')
                    ->label('Type')
                    ->options([
                        'dine_in' => 'Dine-in',
                        'takeaway' => 'Takeaway',
                        'delivery' => 'Delivery',
                    ])
                    ->native(false),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('View')
                    ->icon('heroicon-o-eye'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
