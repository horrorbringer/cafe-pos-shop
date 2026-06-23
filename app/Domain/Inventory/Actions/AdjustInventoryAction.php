<?php

namespace App\Domain\Inventory\Actions;

use App\Domain\Inventory\Models\InventoryItem;
use App\Domain\Inventory\Models\StockMovement;
use App\Domain\Notifications\Events\LowStockDetected;
use App\Domain\Shared\Enums\StockMovementType;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdjustInventoryAction
{
    protected int $lowStockThreshold = 5;

    public function execute(
        InventoryItem $item,
        float $quantity,
        StockMovementType $type,
        User $user,
        ?string $notes = null,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): StockMovement {
        return DB::transaction(function () use ($item, $quantity, $type, $user, $notes, $referenceType, $referenceId) {
            $previousBalance = $item->quantity;

            match ($type) {
                StockMovementType::In => $newBalance = $previousBalance + $quantity,
                StockMovementType::Out => $newBalance = $previousBalance - $quantity,
                StockMovementType::Adjustment => $newBalance = $quantity,
            };

            if ($newBalance < 0) {
                throw new \RuntimeException('Insufficient stock. Available: '.$previousBalance.', Requested: '.$quantity);
            }

            $item->update(['quantity' => $newBalance]);

            if ($newBalance <= $this->lowStockThreshold && $previousBalance > $this->lowStockThreshold) {
                event(new LowStockDetected(
                    product: $item->product,
                    currentStock: (int) $newBalance,
                    threshold: $this->lowStockThreshold,
                ));
            }

            return $item->stockMovements()->create([
                'type' => $type,
                'quantity' => $quantity,
                'running_balance' => $newBalance,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
                'user_id' => $user->id,
            ]);
        });
    }
}
