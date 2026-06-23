<?php

namespace App\Domain\Reporting\Actions;

use App\Domain\Ordering\Models\Order;
use App\Domain\Shared\Enums\OrderStatus;
use Carbon\Carbon;

class GenerateDailySalesReportAction
{
    public function execute(?Carbon $date = null, ?int $branchId = null): array
    {
        $date = $date ?? today();

        $query = Order::whereDate('created_at', $date)
            ->whereIn('status', [OrderStatus::Paid, OrderStatus::Completed]);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $orders = $query->get();

        $totalSales = $orders->sum('total');
        $totalOrders = $orders->count();
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        $paymentMethods = $orders->flatMap->payments->groupBy('method')
            ->map(fn ($payments) => [
                'count' => $payments->count(),
                'total' => $payments->sum('amount'),
            ]);

        $topProducts = $orders->flatMap->items
            ->groupBy('product_id')
            ->map(fn ($items) => [
                'product_id' => $items->first()->product_id,
                'product_name' => $items->first()->product->name ?? 'Unknown',
                'quantity' => $items->sum('quantity'),
                'total' => $items->sum('total_price'),
            ])
            ->sortByDesc('quantity')
            ->take(10)
            ->values();

        return [
            'date' => $date->format('Y-m-d'),
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'average_order_value' => round($averageOrderValue, 2),
            'payment_methods' => $paymentMethods,
            'top_products' => $topProducts,
        ];
    }
}
