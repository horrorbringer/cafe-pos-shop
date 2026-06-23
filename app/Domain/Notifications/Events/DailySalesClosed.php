<?php

declare(strict_types=1);

namespace App\Domain\Notifications\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DailySalesClosed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly string $date,
        public readonly float $totalSales,
        public readonly int $orderCount,
        public readonly array $topProducts,
    ) {}
}
