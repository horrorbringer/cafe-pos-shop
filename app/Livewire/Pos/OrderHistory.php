<?php

namespace App\Livewire\Pos;

use App\Domain\Ordering\Models\Order;
use App\Domain\Shared\Enums\OrderStatus;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class OrderHistory extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $dateFilter = '';

    public ?int $viewingOrderId = null;

    #[Computed]
    public function orders(): array
    {
        $query = Order::with(['user', 'items.product'])
            ->latest();

        if ($this->search) {
            $query->where('order_number', 'like', "%{$this->search}%");
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->dateFilter) {
            $query->whereDate('created_at', $this->dateFilter);
        }

        return $query->paginate(15)->toArray();
    }

    #[Computed]
    public function viewingOrder(): ?array
    {
        if (! $this->viewingOrderId) {
            return null;
        }

        return Order::with(['user', 'items.product', 'payments', 'statusLogs.user'])
            ->findOrFail($this->viewingOrderId)
            ->toArray();
    }

    public function viewOrder(int $orderId): void
    {
        $this->viewingOrderId = $orderId;
    }

    public function closeOrder(): void
    {
        $this->viewingOrderId = null;
    }

    public function getStatusColor(string $status): string
    {
        return OrderStatus::tryFrom($status)?->color() ?? 'gray';
    }

    public function render()
    {
        return view('livewire.pos.order-history');
    }
}
