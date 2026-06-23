<?php

namespace App\Domain\Ordering\Actions;

use App\Domain\Inventory\Actions\RestoreInventoryAction;
use App\Domain\Notifications\Events\RefundVoided;
use App\Domain\Ordering\Models\Order;
use App\Domain\Shared\Enums\OrderStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RefundOrderAction
{
    public function __construct(
        protected TransitionOrderStatusAction $transitionStatus,
        protected RestoreInventoryAction $restoreInventory,
    ) {}

    public function execute(Order $order, User $user, string $reason): Order
    {
        if (! $user->can('manage-orders') && ! $user->hasRole('admin')) {
            throw new \RuntimeException('Only managers and admins can process refunds.');
        }

        if (empty(trim($reason))) {
            throw new \RuntimeException('A reason is required to process a refund.');
        }

        if (! $order->canTransitionTo(OrderStatus::Refunded)) {
            throw new \RuntimeException(
                "Cannot refund order in {$order->status->value} status."
            );
        }

        return DB::transaction(function () use ($order, $user, $reason) {
            $totalPaid = $order->payments()->where('status', 'paid')->sum('amount');

            $this->restoreInventory->execute($order);

            // Create refund payment record (negative amount)
            $order->payments()->create([
                'provider_code' => 'refund',
                'method' => 'other',
                'amount' => -$totalPaid,
                'currency' => config('payment.khqr.default_currency', 'USD'),
                'status' => 'refunded',
                'provider_reference' => 'refund-'.$order->order_number,
            ]);

            // Mark original paid payments as refunded
            $order->payments()->where('status', 'paid')->update(['status' => 'refunded']);

            $order = $this->transitionStatus->execute(
                $order,
                OrderStatus::Refunded,
                $user->id,
                "Refunded. Reason: {$reason}",
            );

            $order->update([
                'amount_paid' => 0,
                'change_amount' => 0,
            ]);

            event(new RefundVoided(
                order: $order,
                user: $user,
                reason: $reason,
                type: 'refund',
            ));

            return $order;
        });
    }
}
