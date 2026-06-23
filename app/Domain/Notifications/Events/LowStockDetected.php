<?php

declare(strict_types=1);

namespace App\Domain\Notifications\Events;

use App\Domain\Inventory\Models\InventoryItem;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LowStockDetected
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly InventoryItem $item,
        public readonly int $currentStock,
        public readonly int $threshold,
    ) {}
}
