<?php

namespace App\Filament\Pages;

use App\Domain\Ordering\Models\Order;
use App\Domain\Shared\Enums\OrderStatus;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Url;

class ReceiptHistory extends Page
{
    protected string $view = 'filament.pages.receipt-history';

    #[Url]
    public string $search = '';

    #[Url]
    public ?string $statusFilter = null;

    #[Url]
    public ?string $dateFrom = null;

    #[Url]
    public ?string $dateTo = null;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return Heroicon::OutlinedReceiptPercent;
    }

    public static function getNavigationSort(): ?int
    {
        return 1;
    }

    public static function getNavigationLabel(): string
    {
        return __('Receipt History');
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Orders';
    }

    public function getTitle(): string
    {
        return __('Receipt History');
    }

    public function getOrders(): LengthAwarePaginator
    {
        return Order::query()
            ->whereIn('status', [OrderStatus::Paid, OrderStatus::Completed, OrderStatus::Refunded])
            ->when($this->search, fn (Builder $q) => $q->where('order_number', 'like', '%'.$this->search.'%'))
            ->when($this->statusFilter, fn (Builder $q) => $q->where('status', $this->statusFilter))
            ->when($this->dateFrom, fn (Builder $q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn (Builder $q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->latest()
            ->paginate(20);
    }

    public function printAction(): Action
    {
        return Action::make('print')
            ->label(__('Print'))
            ->icon('heroicon-o-printer')
            ->color('gray')
            ->action(fn (array $arguments) => $this->redirect(route('admin.receipt.show', $arguments['order']), true));
    }

    public function printPdfAction(): Action
    {
        return Action::make('printPdf')
            ->label(__('PDF'))
            ->icon('heroicon-o-arrow-down-tray')
            ->color('gray')
            ->link()
            ->action(fn (array $arguments) => $this->redirect(route('admin.receipt.pdf', $arguments['order']), true));
    }
}
