<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->circular()
                    ->disk('public')
                    ->defaultImageUrl(url('/images/placeholder.svg'))
                    ->size(44),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn ($record) => Str::limit($record->description, 60)),

                TextColumn::make('category.name')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('price')
                    ->money('USD')
                    ->sortable()
                    ->description(fn ($record) => $record->hasVariants()
                        ? '$'.number_format($record->min_price, 2).' – $'.number_format($record->max_price, 2)
                        : null),

                TextColumn::make('variants_count')
                    ->counts('variants')
                    ->label('Variants')
                    ->badge()
                    ->color('gray')
                    ->toggleable(),

                TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => $state <= 0 ? 'danger' : ($state <= 5 ? 'warning' : 'success'))
                    ->formatStateUsing(fn (int $state): string => $state <= 0 ? 'Out' : (string) $state)
                    ->icon(fn (int $state): string => $state <= 0 ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->toggleable(),

                TextColumn::make('is_available')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Available' : 'Sold Out')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->icon(fn (bool $state): string => $state ? 'heroicon-m-check' : 'heroicon-m-x-mark'),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Category')
                    ->native(false),

                SelectFilter::make('is_available')
                    ->label('Status')
                    ->options([
                        1 => 'Available',
                        0 => 'Sold Out',
                    ])
                    ->native(false),

                SelectFilter::make('stock_quantity')
                    ->label('Stock Level')
                    ->options([
                        'low' => 'Low Stock (<= 5)',
                        'out' => 'Out of Stock',
                    ])
                    ->query(fn ($query, $state) => match ($state) {
                        'low' => $query->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 5),
                        'out' => $query->where('stock_quantity', '<=', 0),
                        default => $query,
                    })
                    ->native(false),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make()
                    ->label('')
                    ->icon('heroicon-o-pencil-square'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
