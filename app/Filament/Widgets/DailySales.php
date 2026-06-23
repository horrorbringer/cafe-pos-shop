<?php

namespace App\Filament\Widgets;

use App\Domain\Ordering\Models\Order;
use App\Domain\Ordering\Models\Payment;
use App\Domain\Shared\Enums\OrderStatus;
use App\Domain\Shared\Enums\PaymentMethod;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DailySales extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $startOfWeek = Carbon::now()->startOfWeek();

        $orders = Order::whereDate('created_at', $today)
            ->whereIn('status', [OrderStatus::Paid, OrderStatus::Completed]);

        $totalSales = (clone $orders)->sum('total');
        $totalOrders = (clone $orders)->count();
        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        $saleIds = (clone $orders)->pluck('id');

        $cashTotal = Payment::whereIn('order_id', $saleIds)
            ->where('method', PaymentMethod::Cash)
            ->where('status', 'paid')
            ->sum('amount');

        $qrTotal = Payment::whereIn('order_id', $saleIds)
            ->where('method', PaymentMethod::Qr)
            ->where('status', 'paid')
            ->sum('amount');

        $yesterdaySales = Order::whereDate('created_at', $yesterday)
            ->whereIn('status', [OrderStatus::Paid, OrderStatus::Completed])
            ->sum('total');

        $salesTrend = $yesterdaySales > 0
            ? round((($totalSales - $yesterdaySales) / $yesterdaySales) * 100, 1)
            : 0;

        $weekSales = Order::whereDate('created_at', '>=', $startOfWeek)
            ->whereDate('created_at', '<=', $today)
            ->whereIn('status', [OrderStatus::Paid, OrderStatus::Completed])
            ->sum('total');

        $completedOrders = (clone $orders)->where('status', OrderStatus::Completed)->count();
        $completionRate = $totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100, 1) : 0;

        return [
            Stat::make('Today\'s Sales', '$'.number_format($totalSales, 2))
                ->description($salesTrend >= 0
                    ? '+'.number_format($salesTrend, 1).'% vs yesterday'
                    : number_format($salesTrend, 1).'% vs yesterday')
                ->descriptionIcon($salesTrend >= 0
                    ? 'heroicon-m-arrow-trending-up'
                    : 'heroicon-m-arrow-trending-down')
                ->color($salesTrend >= 0 ? 'success' : 'danger')
                ->chart($this->getHourlyTrend()),

            Stat::make('Week to Date', '$'.number_format($weekSales, 2))
                ->description('Since '.$startOfWeek->format('D M d'))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Orders', $totalOrders)
                ->description(number_format($completionRate, 1).'% completed')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary'),

            Stat::make('Avg Order', '$'.number_format($averageOrderValue, 2))
                ->description($totalOrders.' transactions today')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('gray'),

            Stat::make('Cash', '$'.number_format($cashTotal, 2))
                ->description('Collected today')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),

            Stat::make('QR', '$'.number_format($qrTotal, 2))
                ->description('Digital payments')
                ->descriptionIcon('heroicon-m-device-phone-mobile')
                ->color('success'),
        ];
    }

    private function getHourlyTrend(): array
    {
        $today = Carbon::today();
        $hours = [];

        for ($h = 0; $h <= now()->hour; $h++) {
            $time = (clone $today)->addHours($h);
            $nextHour = (clone $time)->addHour();

            $hourTotal = Order::whereIn('status', [OrderStatus::Paid, OrderStatus::Completed])
                ->where('created_at', '>=', $time)
                ->where('created_at', '<', $nextHour)
                ->sum('total');

            $hours[] = round($hourTotal, 2);
        }

        return $hours;
    }
}
