<?php

namespace App\Livewire\Pos;

use App\Domain\Ordering\Models\Order;
use App\Domain\Shared\Enums\OrderStatus;
use App\Models\Setting;
use Livewire\Attributes\Computed;
use Livewire\Component;

class CustomerDisplay extends Component
{
    public function render()
    {
        return view('livewire.pos.customer-display');
    }

    #[Computed]
    public function activeOrder(): ?Order
    {
        return Order::with(['items.modifiers', 'user'])
            ->whereIn('status', [OrderStatus::Draft, OrderStatus::Pending, OrderStatus::Paid])
            ->latest()
            ->first();
    }

    #[Computed]
    public function settings(): array
    {
        return [
            'store_name' => Setting::where('key', 'store_name')->value('value') ?? config('app.name'),
            'currency' => config('pos.currency', 'USD'),
        ];
    }
}
