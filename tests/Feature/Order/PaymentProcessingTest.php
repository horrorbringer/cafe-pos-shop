<?php

namespace Tests\Feature\Order;

use App\Domain\Ordering\Actions\ProcessPaymentAction;
use App\Domain\Ordering\Models\Order;
use App\Domain\Shared\Enums\OrderStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class PaymentProcessingTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_can_process_cash_payment(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()
            ->draft()
            ->create(['total' => 45.00, 'user_id' => $user->id]);

        $result = app(ProcessPaymentAction::class)->execute(
            order: $order,
            amountPaid: 50.00,
            paymentMethod: 'cash',
            user: $user,
        );

        $this->assertEquals(OrderStatus::Paid, $result->status);
        $this->assertEquals(50.00, $result->amount_paid);
        $this->assertEquals(5.00, $result->change_amount);

        $payments = $result->payments;
        $this->assertCount(1, $payments);
        $this->assertEquals('paid', $payments->first()->status);
    }

    public function test_exact_payment_has_no_change(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()
            ->draft()
            ->create(['total' => 45.00, 'user_id' => $user->id]);

        $result = app(ProcessPaymentAction::class)->execute(
            order: $order,
            amountPaid: 45.00,
            paymentMethod: 'cash',
            user: $user,
        );

        $this->assertEquals(OrderStatus::Paid, $result->status);
        $this->assertEquals(45.00, $result->amount_paid);
        $this->assertEquals(0, $result->change_amount);
    }

    public function test_idempotency_key_prevents_duplicate_payments(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()
            ->draft()
            ->create(['total' => 20.00, 'user_id' => $user->id]);

        $key = 'test-idempotency-key-123';

        $result1 = app(ProcessPaymentAction::class)->execute(
            order: $order,
            amountPaid: 20.00,
            paymentMethod: 'cash',
            user: $user,
            idempotencyKey: $key,
        );

        $order->update(['status' => OrderStatus::Draft]);

        $result2 = app(ProcessPaymentAction::class)->execute(
            order: $order->fresh(),
            amountPaid: 20.00,
            paymentMethod: 'cash',
            user: $user,
            idempotencyKey: $key,
        );

        $this->assertCount(1, $result2->payments);
    }

    public function test_draft_order_transitions_to_pending_on_payment(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()
            ->draft()
            ->create(['total' => 10.00, 'user_id' => $user->id]);

        app(ProcessPaymentAction::class)->execute(
            order: $order,
            amountPaid: 10.00,
            paymentMethod: 'cash',
            user: $user,
        );

        $statusLogs = $order->fresh()->statusLogs;
        $this->assertGreaterThanOrEqual(2, $statusLogs->count());
    }

    public function test_underpayment_does_not_complete_order(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()
            ->draft()
            ->create(['total' => 50.00, 'user_id' => $user->id]);

        $result = app(ProcessPaymentAction::class)->execute(
            order: $order,
            amountPaid: 30.00,
            paymentMethod: 'cash',
            user: $user,
        );

        $this->assertEquals(OrderStatus::Pending, $result->status);
        $this->assertEquals(30.00, $result->amount_paid);
    }

    public function test_multiple_partial_payments_complete_order(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()
            ->draft()
            ->create(['total' => 50.00, 'user_id' => $user->id]);

        $order = app(ProcessPaymentAction::class)->execute(
            order: $order,
            amountPaid: 30.00,
            paymentMethod: 'cash',
            user: $user,
        );

        $order = app(ProcessPaymentAction::class)->execute(
            order: $order,
            amountPaid: 20.00,
            paymentMethod: 'cash',
            user: $user,
        );

        $this->assertEquals(OrderStatus::Paid, $order->status);
        $this->assertEquals(50.00, $order->amount_paid);
        $this->assertCount(2, $order->payments);
    }
}
