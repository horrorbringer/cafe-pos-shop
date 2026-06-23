<?php

namespace App\Domain\Inventory\Actions;

use App\Domain\Inventory\Models\StockMovement;
use App\Domain\Ordering\Models\Order;
use App\Domain\Shared\Enums\StockMovementType;
use Illuminate\Support\Facades\DB;

class RestoreInventoryAction
{
    public function execute(Order $order): void
    {
        $movements = StockMovement::where('reference_type', 'order')
            ->where('reference_id', $order->id)
            ->where('type', StockMovementType::Out)
            ->get();

        foreach ($movements as $movement) {
            $item = $movement->inventoryItem;
            if ($item === null) {
                continue;
            }

            DB::transaction(function () use ($item, $movement, $order) {
                $previousBalance = $item->quantity;
                $newBalance = $previousBalance + $movement->quantity;

                $item->update(['quantity' => $newBalance]);

                $item->stockMovements()->create([
                    'type' => StockMovementType::In,
                    'quantity' => $movement->quantity,
                    'running_balance' => $newBalance,
                    'reference_type' => 'refund',
                    'reference_id' => $order->id,
                    'notes' => "Refund for order {$order->order_number}: {$movement->notes}",
                    'user_id' => $order->user_id,
                ]);
            });
        }
    }
}
