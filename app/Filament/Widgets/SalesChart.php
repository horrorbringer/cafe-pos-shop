<?php

namespace App\Filament\Widgets;

use App\Domain\Ordering\Models\Order;
use App\Domain\Shared\Enums\OrderStatus;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class SalesChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = [
        'lg' => 2,
        'md' => 'full',
        'sm' => 'full',
    ];

    public ?string $filter = 'week';

    protected function getData(): array
    {
        $days = match ($this->filter) {
            'month' => 30,
            'week' => 7,
            default => 7,
        };

        $labels = collect();
        $sales = collect();
        $orders = collect();

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels->push($date->format($days > 7 ? 'M d' : 'D'));

            $daySales = Order::whereDate('created_at', $date)
                ->whereIn('status', [OrderStatus::Paid, OrderStatus::Completed])
                ->sum('total');

            $dayOrders = Order::whereDate('created_at', $date)
                ->whereIn('status', [OrderStatus::Paid, OrderStatus::Completed])
                ->count();

            $sales->push(round($daySales, 2));
            $orders->push($dayOrders);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data' => $sales->toArray(),
                    'borderColor' => '#d97706',
                    'backgroundColor' => 'rgba(217, 119, 6, 0.08)',
                    'fill' => true,
                    'tension' => 0.35,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                    'pointBackgroundColor' => '#d97706',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Orders',
                    'data' => $orders->toArray(),
                    'borderColor' => '#6366f1',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.05)',
                    'fill' => false,
                    'tension' => 0.35,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 6,
                    'pointBackgroundColor' => '#6366f1',
                    'borderWidth' => 2,
                    'borderDash' => [5, 5],
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $labels->toArray(),
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            'week' => 'This Week',
            'month' => 'This Month',
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                        'boxWidth' => 8,
                    ],
                ],
                'tooltip' => [
                    'backgroundColor' => '#1c1917',
                    'titleColor' => '#f5f5f4',
                    'bodyColor' => '#d6d3d1',
                    'cornerRadius' => 8,
                    'padding' => 12,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'position' => 'left',
                    'grid' => [
                        'color' => 'rgba(0,0,0,0.05)',
                    ],
                    'ticks' => [
                        'callback' => '$',
                    ],
                ],
                'y1' => [
                    'beginAtZero' => true,
                    'position' => 'right',
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
        ];
    }

    public function getHeading(): ?string
    {
        return match ($this->filter) {
            'month' => '30-Day Sales',
            default => '7-Day Sales',
        };
    }

    protected function getType(): string
    {
        return 'line';
    }
}
