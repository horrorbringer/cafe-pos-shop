<?php

namespace App\Domain\Ordering\Actions;

use App\Domain\Ordering\Models\Order;

class RecalculateOrderTotalsAction
{
    public function execute(Order $order): void
    {
        $subtotal = $order->items->sum('total_price');
        $taxRate = config('pos.tax_rate', 0.10);
        $tax = round($subtotal * $taxRate, 2);
        $total = $subtotal + $tax - $order->discount;

        $order->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => max(0, $total),
        ]);
    }
}
