<?php

use App\Domain\Notifications\Events\DailySalesClosed;
use App\Domain\Ordering\Models\Order;
use App\Domain\Ordering\Models\OrderItem;
use App\Domain\Shared\Enums\OrderStatus;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $today = Carbon::yesterday();

    $orders = Order::whereDate('created_at', $today)
        ->whereIn('status', [OrderStatus::Paid, OrderStatus::Completed]);

    $totalSales = (clone $orders)->sum('total');
    $orderCount = (clone $orders)->count();

    $topProducts = OrderItem::whereHas('order', function ($query) use ($today) {
        $query->whereDate('created_at', $today)
            ->whereIn('status', [OrderStatus::Paid, OrderStatus::Completed]);
    })
        ->selectRaw('product_id, sum(quantity) as total_qty, sum(total_price) as total_revenue')
        ->groupBy('product_id')
        ->orderByDesc('total_revenue')
        ->limit(5)
        ->with('product')
        ->get()
        ->map(fn ($item) => [
            'name' => $item->product?->name ?? 'Unknown',
            'revenue' => $item->total_revenue,
        ])
        ->toArray();

    event(new DailySalesClosed(
        date: $today->toDateString(),
        totalSales: $totalSales,
        orderCount: $orderCount,
        topProducts: $topProducts,
    ));
})->dailyAt('23:59')->name('daily-sales-summary');
