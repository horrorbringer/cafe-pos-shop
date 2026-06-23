<?php

namespace App\Filament\Widgets;

use App\Domain\Ordering\Models\OrderItem;
use App\Domain\Shared\Enums\OrderStatus;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class TopProducts extends Widget
{
    protected static ?int $sort = 3;

    protected string $view = 'filament.widgets.top-products';

    protected int|string|array $columnSpan = [
        'lg' => 1,
        'md' => 'full',
        'sm' => 'full',
    ];

    public function getTopProducts(): array
    {
        $today = Carbon::today();

        $items = OrderItem::query()
            ->whereHas('order', function ($query) use ($today) {
                $query->whereDate('created_at', $today)
                    ->whereIn('status', [OrderStatus::Paid, OrderStatus::Completed]);
            })
            ->with('product')
            ->selectRaw('product_id, sum(quantity) as total_quantity, sum(total_price) as total_revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        $maxQty = $items->max('total_quantity') ?: 1;

        return $items->map(function ($item, $index) use ($maxQty) {
            $name = $item->product?->name ?? 'Unknown';
            $pct = $maxQty > 0 ? round(($item->total_quantity / $maxQty) * 100) : 0;

            return [
                'rank' => $index + 1,
                'name' => $name,
                'quantity' => $item->total_quantity,
                'revenue' => $item->total_revenue,
                'pct' => $pct,
            ];
        })->toArray();
    }
}
