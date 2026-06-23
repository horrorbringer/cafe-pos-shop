<?php

namespace App\Domain\Ordering\Actions;

use App\Domain\Ordering\Models\Order;
use App\Domain\Shared\Enums\OrderStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CancelOrderAction
{
    public function __construct(
        protected TransitionOrderStatusAction $transitionStatus,
    ) {}

    public function execute(Order $order, User $user, ?string $reason = null): Order
    {
        if (! $order->canTransitionTo(OrderStatus::Cancelled)) {
            throw new \RuntimeException(
                "Cannot cancel order in {$order->status->value} status."
            );
        }

        return DB::transaction(function () use ($order, $user, $reason) {
            // Restore stock if items were deducted (not for draft orders)
            if ($order->status !== OrderStatus::Draft) {
                foreach ($order->items as $item) {
                    $item->product?->increment('stock_quantity', $item->quantity);
                }
            }

            $order = $this->transitionStatus->execute(
                $order,
                OrderStatus::Cancelled,
                $user->id,
                $reason ?? 'Order cancelled',
            );

            return $order;
        });
    }
}
