<?php

declare(strict_types=1);

namespace App\Domain\Notifications\Events;

use App\Models\Product;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LowStockDetected
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Product $product,
        public readonly int $currentStock,
        public readonly int $threshold,
    ) {}
}
