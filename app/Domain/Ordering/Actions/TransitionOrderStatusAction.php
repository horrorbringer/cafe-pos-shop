<?php

namespace App\Domain\Ordering\Actions;

use App\Domain\Ordering\Models\Order;
use App\Domain\Shared\Enums\OrderStatus;
use Illuminate\Support\Facades\DB;

class TransitionOrderStatusAction
{
    public function execute(Order $order, OrderStatus $toStatus, ?int $userId = null, string $notes = ''): Order
    {
        if (! $order->canTransitionTo($toStatus)) {
            throw new \RuntimeException(
                "Cannot transition from {$order->status->value} to {$toStatus->value}."
            );
        }

        return DB::transaction(function () use ($order, $toStatus, $userId, $notes) {
            $fromStatus = $order->status;

            $order->update(['status' => $toStatus]);

            $order->statusLogs()->create([
                'from_status' => $fromStatus?->value,
                'to_status' => $toStatus->value,
                'user_id' => $userId ?? $order->user_id,
                'notes' => $notes,
            ]);

            return $order->fresh();
        });
    }
}
