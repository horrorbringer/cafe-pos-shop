<?php

namespace App\Filament\Resources\Products\Tables;

use App\Domain\Catalog\Models\Product;
use App\Support\FeatureFlags;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        $withVariants = FeatureFlags::variantsEnabled();
        $withInventory = FeatureFlags::inventoryEnabled();

        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->when(
                $withVariants,
                fn ($q) => $q->with('variants'),
                fn ($q) => $q,
            )->with('category'))
            ->columns(array_filter([
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

                $withVariants
                    ? TextColumn::make('variants_count')
                        ->counts('variants')
                        ->label('Variants')
                        ->badge()
                        ->color('gray')
                        ->toggleable()
                    : null,

                $withInventory
                    ? TextColumn::make('stock_quantity')
                        ->label('Stock')
                        ->sortable()
                        ->badge()
                        ->color(fn (int $state): string => $state <= 0 ? 'danger' : ($state <= 5 ? 'warning' : 'success'))
                        ->formatStateUsing(fn (int $state): string => $state <= 0 ? 'Out' : (string) $state)
                        ->icon(fn (int $state): string => $state <= 0 ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                        ->toggleable()
                    : null,

                TextColumn::make('is_available')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Available' : 'Sold Out')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->icon(fn (bool $state): string => $state ? 'heroicon-m-check' : 'heroicon-m-x-mark'),
            ]))
            ->filters(array_filter([
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

                $withInventory
                    ? SelectFilter::make('stock_quantity')
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
                        ->native(false)
                    : null,
            ]))
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make()
                    ->label('')
                    ->icon('heroicon-o-pencil-square'),

                Action::make('duplicate')
                    ->label('Duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('gray')
                    ->action(fn (Product $record) => static::duplicateProduct($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('toggleAvailability')
                        ->label('Mark as Available')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each->update(['is_available' => true]))
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('toggleUnavailable')
                        ->label('Mark as Sold Out')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn (Collection $records) => $records->each->update(['is_available' => false]))
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('changeCategory')
                        ->label('Change Category')
                        ->icon('heroicon-o-folder')
                        ->color('warning')
                        ->form([
                            Select::make('category_id')
                                ->label('New Category')
                                ->relationship('category', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->native(false),
                        ])
                        ->action(fn (Collection $records, array $data) => $records->each->update([
                            'category_id' => $data['category_id'],
                        ]))
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('bulkPrice')
                        ->label('Adjust Price')
                        ->icon('heroicon-o-currency-dollar')
                        ->color('gray')
                        ->form([
                            Select::make('type')
                                ->label('Adjustment Type')
                                ->options([
                                    'set' => 'Set to exact amount',
                                    'add' => 'Add amount',
                                    'percent' => 'Add percentage',
                                ])
                                ->required()
                                ->native(false)
                                ->default('add'),
                            TextInput::make('value')
                                ->label('Value')
                                ->numeric()
                                ->required()
                                ->minValue(0),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $value = (float) $data['value'];
                            $type = $data['type'];

                            $records->each(function (Product $product) use ($value, $type) {
                                $newPrice = match ($type) {
                                    'set' => $value,
                                    'add' => $product->price + $value,
                                    'percent' => $product->price * (1 + $value / 100),
                                    default => $product->price,
                                };

                                if ($newPrice >= 0) {
                                    $product->update(['price' => round($newPrice, 2)]);
                                }
                            });
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function duplicateProduct(Product $product): void
    {
        $clone = $product->replicate(['slug']);
        $clone->name = $product->name.' (copy)';
        $clone->slug = Str::slug($clone->name);
        $clone->save();

        foreach ($product->variants as $variant) {
            $clone->variants()->create($variant->replicate()->toArray());
        }

        $clone->modifierGroups()->sync($product->modifierGroups->pluck('id'));

        Notification::make()
            ->success()
            ->title('Product duplicated')
            ->body('"'.$product->name.'" has been duplicated as "'.$clone->name.'".')
            ->send();
    }
}
