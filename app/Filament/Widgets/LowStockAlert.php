<?php

namespace App\Filament\Widgets;

use App\Domain\Inventory\Models\InventoryItem;
use App\Filament\Resources\InventoryItems\InventoryItemResource;
use Filament\Widgets\Widget;

class LowStockAlert extends Widget
{
    protected static ?int $sort = 4;

    protected string $view = 'filament.widgets.low-stock-alert';

    protected int|string|array $columnSpan = [
        'lg' => 2,
        'md' => 'full',
        'sm' => 'full',
    ];

    public function getLowStockItems(): array
    {
        return InventoryItem::query()
            ->whereColumn('quantity', '<=', 'minimum_quantity')
            ->orderByRaw('CAST(quantity AS DECIMAL) / CAST(minimum_quantity AS DECIMAL)')
            ->limit(5)
            ->get(['id', 'name', 'quantity', 'minimum_quantity', 'unit'])
            ->map(function ($item) {
                $needed = max(0, $item->minimum_quantity - $item->quantity);

                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'quantity' => $item->quantity,
                    'minimum' => $item->minimum_quantity,
                    'needed' => $needed,
                    'unit' => $item->unit,
                    'edit_url' => InventoryItemResource::getUrl('edit', ['record' => $item->id]),
                    'ratio' => $item->minimum_quantity > 0
                        ? round(($item->quantity / $item->minimum_quantity) * 100)
                        : 0,
                ];
            })->toArray();
    }

    public function getTotalLowStock(): int
    {
        return InventoryItem::query()
            ->whereColumn('quantity', '<=', 'minimum_quantity')
            ->count();
    }

    public function getInventoryUrl(): string
    {
        return InventoryItemResource::getUrl('index');
    }
}
