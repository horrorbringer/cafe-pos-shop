<?php

namespace App\Domain\Ordering\Actions;

use App\Domain\Inventory\Actions\DeductInventoryAction;
use App\Domain\Notifications\Events\OrderPaid;
use App\Domain\Ordering\Models\Order;
use App\Domain\Shared\Enums\OrderStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CompleteOrderAction
{
    public function __construct(
        protected TransitionOrderStatusAction $transitionStatus,
        protected DeductInventoryAction $deductInventory,
    ) {}

    public function execute(Order $order, User $user): Order
    {
        return DB::transaction(function () use ($order, $user) {
            $order = $this->transitionStatus->execute(
                $order,
                OrderStatus::Completed,
                $user->id,
                'Order completed',
            );

            $this->deductInventory->execute($order);

            event(new OrderPaid(
                order: $order,
                amountPaid: $order->amount_paid,
                paymentMethod: $order->payments()->latest()->first()?->method?->value ?? 'unknown',
            ));

            return $order;
        });
    }
}
