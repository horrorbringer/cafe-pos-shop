<?php

namespace Tests\Feature\Order;

use App\Domain\Catalog\Models\Product;
use App\Domain\Ordering\Actions\CancelOrderAction;
use App\Domain\Ordering\Models\Order;
use App\Domain\Shared\Enums\OrderStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class OrderCancellationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_can_cancel_draft_order(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->draft()->create();

        $result = app(CancelOrderAction::class)->execute($order, $user);

        $this->assertEquals(OrderStatus::Cancelled, $result->status);
        $this->assertCount(1, $result->statusLogs);
    }

    public function test_cancelling_restores_stock(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'stock_quantity' => 50,
        ]);

        $order = Order::factory()->pending()->create();

        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 3,
            'unit_price' => $product->price,
            'total_price' => $product->price * 3,
        ]);

        // Deduct stock to simulate adding item
        $product->decrement('stock_quantity', 3);
        $this->assertEquals(47, $product->fresh()->stock_quantity);

        app(CancelOrderAction::class)->execute($order, $user);

        $this->assertEquals(50, $product->fresh()->stock_quantity);
    }

    public function test_can_cancel_pending_order(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->pending()->create();

        $result = app(CancelOrderAction::class)->execute($order, $user);

        $this->assertEquals(OrderStatus::Cancelled, $result->status);
    }

    public function test_paid_order_cannot_be_cancelled(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot cancel order in paid status');

        $user = User::factory()->create();
        $order = Order::factory()->paid()->create();

        app(CancelOrderAction::class)->execute($order, $user);
    }

    public function test_completed_order_cannot_be_cancelled(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot cancel order in completed status');

        $user = User::factory()->create();
        $order = Order::factory()->completed()->create();

        app(CancelOrderAction::class)->execute($order, $user);
    }

    public function test_already_cancelled_order_cannot_be_cancelled_again(): void
    {
        $this->expectException(\RuntimeException::class);

        $user = User::factory()->create();
        $order = Order::factory()->cancelled()->create();

        app(CancelOrderAction::class)->execute($order, $user);
    }
}
