<?php

declare(strict_types=1);

namespace App\Domain\Notifications\Events;

use App\Domain\Ordering\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentFailed
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Order $order,
        public readonly string $reason,
        public readonly string $paymentMethod,
    ) {}
}
