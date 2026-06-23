<?php

namespace App\Domain\Notifications\Events;

use App\Domain\Ordering\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RefundVoided
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Order $order,
        public readonly User $user,
        public readonly string $reason,
        public readonly string $type,
    ) {}
}
