<?php

namespace Tests\Feature\Order;

use App\Domain\Ordering\Actions\TransitionOrderStatusAction;
use App\Domain\Ordering\Models\Order;
use App\Domain\Shared\Enums\OrderStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class OrderStateMachineTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_draft_can_transition_to_pending(): void
    {
        $order = Order::factory()->draft()->create();

        $this->assertTrue($order->canTransitionTo(OrderStatus::Pending));
    }

    public function test_draft_can_transition_to_cancelled(): void
    {
        $order = Order::factory()->draft()->create();

        $this->assertTrue($order->canTransitionTo(OrderStatus::Cancelled));
    }

    public function test_draft_cannot_skip_to_paid(): void
    {
        $order = Order::factory()->draft()->create();

        $this->assertFalse($order->canTransitionTo(OrderStatus::Paid));
    }

    public function test_draft_cannot_skip_to_completed(): void
    {
        $order = Order::factory()->draft()->create();

        $this->assertFalse($order->canTransitionTo(OrderStatus::Completed));
    }

    public function test_pending_can_transition_to_paid(): void
    {
        $order = Order::factory()->pending()->create();

        $this->assertTrue($order->canTransitionTo(OrderStatus::Paid));
    }

    public function test_pending_can_transition_to_cancelled(): void
    {
        $order = Order::factory()->pending()->create();

        $this->assertTrue($order->canTransitionTo(OrderStatus::Cancelled));
    }

    public function test_paid_can_transition_to_completed(): void
    {
        $order = Order::factory()->paid()->create();

        $this->assertTrue($order->canTransitionTo(OrderStatus::Completed));
    }

    public function test_paid_can_transition_to_refunded(): void
    {
        $order = Order::factory()->paid()->create();

        $this->assertTrue($order->canTransitionTo(OrderStatus::Refunded));
    }

    public function test_completed_can_transition_to_refunded(): void
    {
        $order = Order::factory()->completed()->create();

        $this->assertTrue($order->canTransitionTo(OrderStatus::Refunded));
    }

    public function test_completed_cannot_transition_back_to_paid(): void
    {
        $order = Order::factory()->completed()->create();

        $this->assertFalse($order->canTransitionTo(OrderStatus::Paid));
    }

    public function test_cancelled_cannot_transition_to_any(): void
    {
        $order = Order::factory()->cancelled()->create();

        $this->assertFalse($order->canTransitionTo(OrderStatus::Draft));
        $this->assertFalse($order->canTransitionTo(OrderStatus::Pending));
        $this->assertFalse($order->canTransitionTo(OrderStatus::Paid));
        $this->assertFalse($order->canTransitionTo(OrderStatus::Completed));
        $this->assertFalse($order->canTransitionTo(OrderStatus::Refunded));
    }

    public function test_transition_creates_status_log(): void
    {
        $order = Order::factory()->draft()->create();
        $user = User::factory()->create();

        $order = app(TransitionOrderStatusAction::class)
            ->execute($order, OrderStatus::Pending, $user->id);

        $this->assertEquals(OrderStatus::Pending, $order->status);

        $log = $order->statusLogs()->latest()->first();
        $this->assertNotNull($log);
        $this->assertEquals(OrderStatus::Draft->value, $log->from_status);
        $this->assertEquals(OrderStatus::Pending->value, $log->to_status);
        $this->assertEquals($user->id, $log->user_id);
    }

    public function test_invalid_transition_throws_exception(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot transition from draft to paid');

        $order = Order::factory()->draft()->create();

        app(TransitionOrderStatusAction::class)
            ->execute($order, OrderStatus::Paid);
    }
}
