<?php

namespace App\Filament\Pages;

use App\Domain\Ordering\Models\Order;
use App\Domain\Ordering\Models\OrderItem;
use App\Domain\Ordering\Models\Payment;
use App\Domain\Shared\Enums\OrderStatus;
use App\Domain\Shared\Enums\PaymentMethod;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Livewire\Attributes\Url;

class ReportsPage extends Page
{
    protected string $view = 'filament.pages.reports-page';

    #[Url]
    public string $dateFilter = 'last-7-days';

    #[Url]
    public ?string $startDate = null;

    #[Url]
    public ?string $endDate = null;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'manager']) ?? false;
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return Heroicon::OutlinedChartBar;
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function getNavigationLabel(): string
    {
        return 'Reports';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Reports';
    }

    public function getTitle(): string
    {
        return __('Sales Reports');
    }

    public function updatedDateFilter(): void
    {
        $this->resetDateRange();
    }

    public function resetDateRange(): void
    {
        $this->startDate = null;
        $this->endDate = null;
    }

    public function getDateRange(): array
    {
        $end = $this->endDate ? Carbon::parse($this->endDate) : Carbon::today();
        $start = match ($this->dateFilter) {
            'today' => Carbon::today(),
            'yesterday' => Carbon::yesterday(),
            'last-7-days' => Carbon::today()->subDays(6),
            'last-30-days' => Carbon::today()->subDays(29),
            'custom' => $this->startDate ? Carbon::parse($this->startDate) : Carbon::today()->subDays(6),
            default => Carbon::today()->subDays(6),
        };

        if ($this->dateFilter === 'custom' && $this->endDate) {
            $end = Carbon::parse($this->endDate);
        }

        return [$start->startOfDay(), $end->endOfDay()];
    }

    protected function getBaseOrderQuery()
    {
        [$start, $end] = $this->getDateRange();

        return Order::whereBetween('created_at', [$start, $end])
            ->whereIn('status', [OrderStatus::Paid, OrderStatus::Completed]);
    }

    public function getTotalSales(): float
    {
        return (float) $this->getBaseOrderQuery()->sum('total');
    }

    public function getCashSales(): float
    {
        [$start, $end] = $this->getDateRange();

        return (float) Payment::whereBetween('paid_at', [$start, $end])
            ->where('method', PaymentMethod::Cash)
            ->where('status', 'paid')
            ->sum('amount');
    }

    public function getKhqrSales(): float
    {
        [$start, $end] = $this->getDateRange();

        return (float) Payment::whereBetween('paid_at', [$start, $end])
            ->where('method', PaymentMethod::Qr)
            ->where('status', 'paid')
            ->sum('amount');
    }

    public function getOrderCount(): int
    {
        return $this->getBaseOrderQuery()->count();
    }

    public function getRefundAmount(): float
    {
        [$start, $end] = $this->getDateRange();

        return (float) Order::whereBetween('created_at', [$start, $end])
            ->where('status', OrderStatus::Refunded)
            ->sum('total');
    }

    public function getTopProducts(): Collection
    {
        [$start, $end] = $this->getDateRange();

        return OrderItem::query()
            ->whereHas('order', function ($query) use ($start, $end) {
                $query->whereBetween('created_at', [$start, $end])
                    ->whereIn('status', [OrderStatus::Paid, OrderStatus::Completed]);
            })
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->selectRaw('order_items.product_id, products.name as product_name, sum(order_items.quantity) as total_quantity, sum(order_items.total_price) as total_revenue')
            ->groupBy('order_items.product_id', 'products.name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();
    }

    public function exportCsvAction(): Action
    {
        return Action::make('exportCsv')
            ->label(__('Export CSV'))
            ->icon('heroicon-o-arrow-down-tray')
            ->action(fn () => $this->exportCsv());
    }

    public function exportCsv()
    {
        $products = $this->getTopProducts();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sales-report-'.now()->format('Y-m-d').'.csv"',
        ];

        $callback = function () use ($products) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Product', 'Quantity Sold', 'Revenue']);

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->product_name ?? 'Unknown',
                    $product->total_quantity,
                    number_format($product->total_revenue, 2),
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->exportCsvAction(),
        ];
    }
}
