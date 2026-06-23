<?php

namespace Tests\Feature\Order;

use App\Domain\Ordering\Actions\CreateOrderAction;
use App\Domain\Ordering\Models\Order;
use App\Domain\Shared\Enums\OrderStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class OrderCreationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_can_create_order_with_defaults(): void
    {
        $user = User::factory()->create();

        $order = app(CreateOrderAction::class)->execute($user);

        $this->assertNotNull($order);
        $this->assertEquals(OrderStatus::Draft, $order->status);
        $this->assertEquals($user->id, $order->user_id);
        $this->assertEquals(0, $order->subtotal);
        $this->assertEquals(0, $order->total);
        $this->assertStringStartsWith('ORD-', $order->order_number);
    }

    public function test_create_dine_in_order_requires_table_number(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Table number is required for dine-in orders.');

        $user = User::factory()->create();

        app(CreateOrderAction::class)->execute($user, orderType: 'dine_in');
    }

    public function test_create_dine_in_order_with_table_number(): void
    {
        $user = User::factory()->create();

        $order = app(CreateOrderAction::class)->execute(
            $user,
            orderType: 'dine_in',
            tableNumber: '5',
        );

        $this->assertEquals('dine_in', $order->order_type->value);
        $this->assertEquals('5', $order->table_number);
    }

    public function test_generate_order_number_is_unique(): void
    {
        $numbers = [];
        $count = 10;

        for ($i = 0; $i < $count; $i++) {
            $numbers[] = Order::generateOrderNumber();
        }

        $this->assertCount($count, array_unique($numbers));
    }

    public function test_create_order_logs_status_history(): void
    {
        $user = User::factory()->create();

        $order = app(CreateOrderAction::class)->execute($user);

        $logs = $order->statusLogs;
        $this->assertCount(1, $logs);
        $this->assertNull($logs->first()->from_status);
        $this->assertEquals(OrderStatus::Draft->value, $logs->first()->to_status);
    }
}
