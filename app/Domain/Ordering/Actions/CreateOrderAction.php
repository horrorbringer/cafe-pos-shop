<?php

namespace App\Domain\Ordering\Actions;

use App\Domain\Ordering\Models\Order;
use App\Domain\Shared\Enums\OrderStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateOrderAction
{
    public function execute(User $user, ?int $branchId = null, ?string $orderType = null, ?string $tableNumber = null): Order
    {
        if ($orderType === 'dine_in' && empty($tableNumber)) {
            throw new \RuntimeException('Table number is required for dine-in orders.');
        }

        return DB::transaction(function () use ($user, $branchId, $orderType, $tableNumber) {
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $user->id,
                'branch_id' => $branchId,
                'order_type' => $orderType ?? 'dine_in',
                'table_number' => $tableNumber,
                'subtotal' => 0,
                'discount' => 0,
                'tax' => 0,
                'total' => 0,
                'status' => OrderStatus::Draft,
                'amount_paid' => 0,
                'change_amount' => 0,
            ]);

            $order->statusLogs()->create([
                'from_status' => null,
                'to_status' => OrderStatus::Draft->value,
                'user_id' => $user->id,
                'notes' => 'Order created',
            ]);

            return $order;
        });
    }
}
