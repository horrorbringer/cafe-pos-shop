<?php

namespace App\Filament\Widgets;

use App\Domain\Inventory\Models\InventoryItem;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class LowStockAlert extends Widget
{
    protected static ?int $sort = 4;

    protected string $view = 'filament.widgets.low-stock-alert';

    public function getLowStockItems(): Collection
    {
        return InventoryItem::query()
            ->whereColumn('quantity', '<=', 'minimum_quantity')
            ->orderBy('quantity')
            ->limit(5)
            ->get(['name', 'quantity', 'unit']);
    }
}
