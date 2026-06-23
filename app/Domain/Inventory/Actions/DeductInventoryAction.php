<?php

namespace App\Domain\Inventory\Actions;

use App\Domain\Notifications\Events\LowStockDetected;
use App\Domain\Ordering\Models\Order;
use App\Domain\Shared\Enums\StockMovementType;
use Illuminate\Support\Facades\DB;

class DeductInventoryAction
{
    public function execute(Order $order): void
    {
        $items = $order->items()->with('product.ingredients.inventoryItem')->get();

        foreach ($items as $orderItem) {
            $product = $orderItem->product;
            if ($product === null) {
                continue;
            }

            $ingredients = $product->ingredients;
            if ($ingredients->isEmpty()) {
                continue;
            }

            foreach ($ingredients as $ingredient) {
                $inventoryItem = $ingredient->inventoryItem;
                if ($inventoryItem === null) {
                    continue;
                }

                $totalRequired = $ingredient->quantity_required * $orderItem->quantity;

                DB::transaction(function () use ($inventoryItem, $totalRequired, $order, $orderItem) {
                    $previousBalance = $inventoryItem->quantity;
                    $newBalance = $previousBalance - $totalRequired;

                    if ($newBalance < 0) {
                        throw new \RuntimeException(
                            "Insufficient {$inventoryItem->name}. Available: {$previousBalance}, Required: {$totalRequired}"
                        );
                    }

                    $inventoryItem->update(['quantity' => $newBalance]);

                    $inventoryItem->stockMovements()->create([
                        'type' => StockMovementType::Out,
                        'quantity' => $totalRequired,
                        'running_balance' => $newBalance,
                        'reference_type' => 'order',
                        'reference_id' => $order->id,
                        'notes' => "Order {$order->order_number}, Item: {$orderItem->product_name} x{$orderItem->quantity}",
                        'user_id' => $order->user_id,
                    ]);

                    if ($newBalance <= $inventoryItem->minimum_quantity && $previousBalance > $inventoryItem->minimum_quantity) {
                        event(new LowStockDetected(
                            item: $inventoryItem,
                            currentStock: (int) $newBalance,
                            threshold: (int) $inventoryItem->minimum_quantity,
                        ));
                    }
                });
            }
        }
    }
}
