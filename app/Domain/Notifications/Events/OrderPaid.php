<?php

declare(strict_types=1);

namespace App\Domain\Notifications\Events;

use App\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPaid
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Order $order,
        public readonly float $amountPaid,
        public readonly string $paymentMethod,
    ) {}
}
