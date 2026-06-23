<?php

namespace App\Domain\Ordering\Actions;

use App\Domain\Ordering\Models\Order;
use App\Domain\Ordering\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class RemoveOrderItemAction
{
    public function __construct(
        protected RecalculateOrderTotalsAction $recalculateOrder,
    ) {}

    public function execute(Order $order, OrderItem $item): void
    {
        DB::transaction(function () use ($order, $item) {
            $item->delete();

            $this->recalculateOrder->execute($order);
        });
    }
}
