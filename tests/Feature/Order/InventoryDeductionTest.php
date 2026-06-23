<?php

namespace Tests\Feature\Order;

use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Models\ProductIngredient;
use App\Domain\Inventory\Actions\DeductInventoryAction;
use App\Domain\Inventory\Models\InventoryItem;
use App\Domain\Ordering\Actions\CompleteOrderAction;
use App\Domain\Ordering\Actions\ProcessPaymentAction;
use App\Domain\Ordering\Actions\RefundOrderAction;
use App\Domain\Ordering\Models\Order;
use App\Domain\Shared\Enums\OrderStatus;
use App\Domain\Shared\Enums\StockMovementType;
use App\Domain\Shop\Models\Branch;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class InventoryDeductionTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function createBranch(): Branch
    {
        return Branch::create(['name' => 'Test Branch', 'is_active' => true]);
    }

    public function test_deducts_inventory_when_order_is_completed(): void
    {
        $branch = $this->createBranch();
        $user = User::factory()->create();

        $inventoryItem = InventoryItem::create([
            'branch_id' => $branch->id,
            'name' => 'Coffee Beans',
            'unit' => 'kg',
            'quantity' => 10,
            'minimum_quantity' => 2,
            'cost_per_unit' => 15,
        ]);

        $product = Product::factory()->create([
            'branch_id' => null,
            'stock_quantity' => 100,
        ]);

        ProductIngredient::create([
            'product_id' => $product->id,
            'inventory_item_id' => $inventoryItem->id,
            'quantity_required' => 0.25,
        ]);

        $order = Order::factory()->draft()->create(['user_id' => $user->id]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 3,
            'unit_price' => $product->price,
            'total_price' => $product->price * 3,
        ]);

        $order = app(ProcessPaymentAction::class)->execute(
            order: $order,
            amountPaid: 100,
            paymentMethod: 'cash',
            user: $user,
        );

        $this->assertEquals(OrderStatus::Paid, $order->status);

        $order = app(CompleteOrderAction::class)->execute($order, $user);

        $this->assertEquals(OrderStatus::Completed, $order->status);

        $this->assertEquals(9.25, $inventoryItem->fresh()->quantity);

        $movements = $inventoryItem->fresh()->stockMovements;
        $this->assertCount(1, $movements);
        $this->assertEquals(StockMovementType::Out, $movements->first()->type);
        $this->assertEquals(0.75, $movements->first()->quantity);
        $this->assertEquals(9.25, $movements->first()->running_balance);
        $this->assertEquals('order', $movements->first()->reference_type);
        $this->assertEquals($order->id, $movements->first()->reference_id);
    }

    public function test_restores_inventory_when_order_is_refunded(): void
    {
        $this->seed(RoleSeeder::class);
        $branch = $this->createBranch();
        $user = User::factory()->create();
        $user->assignRole('admin');

        $inventoryItem = InventoryItem::create([
            'branch_id' => $branch->id,
            'name' => 'Milk',
            'unit' => 'L',
            'quantity' => 20,
            'minimum_quantity' => 5,
            'cost_per_unit' => 2.5,
        ]);

        $product = Product::factory()->create([
            'branch_id' => null,
            'stock_quantity' => 50,
        ]);

        ProductIngredient::create([
            'product_id' => $product->id,
            'inventory_item_id' => $inventoryItem->id,
            'quantity_required' => 0.5,
        ]);

        $order = Order::factory()->draft()->create([
            'total' => 15,
            'user_id' => $user->id,
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'unit_price' => 7.5,
            'total_price' => 15,
        ]);

        $order = app(ProcessPaymentAction::class)->execute(
            order: $order,
            amountPaid: 15,
            paymentMethod: 'cash',
            user: $user,
        );

        $order = app(CompleteOrderAction::class)->execute($order, $user);

        $this->assertEquals(19, $inventoryItem->fresh()->quantity);

        $order = app(RefundOrderAction::class)->execute($order, $user, 'Customer requested refund');

        $this->assertEquals(OrderStatus::Refunded, $order->status);

        $inventoryItem->refresh();
        $this->assertEquals(20, $inventoryItem->quantity);

        $movements = $inventoryItem->stockMovements;
        $this->assertCount(2, $movements);

        $restoreMovement = $movements->last();
        $this->assertEquals(StockMovementType::In, $restoreMovement->type);
        $this->assertEquals(1, $restoreMovement->quantity);
        $this->assertEquals(20, $restoreMovement->running_balance);
    }

    public function test_deduct_with_multiple_ingredients(): void
    {
        $branch = $this->createBranch();
        $user = User::factory()->create();

        $coffeeBeans = InventoryItem::create([
            'branch_id' => $branch->id, 'name' => 'Coffee Beans', 'unit' => 'kg', 'quantity' => 10, 'minimum_quantity' => 2, 'cost_per_unit' => 15,
        ]);

        $milk = InventoryItem::create([
            'branch_id' => $branch->id, 'name' => 'Milk', 'unit' => 'L', 'quantity' => 20, 'minimum_quantity' => 5, 'cost_per_unit' => 2.5,
        ]);

        $product = Product::factory()->create([
            'branch_id' => null,
            'stock_quantity' => 100,
        ]);

        ProductIngredient::create(['product_id' => $product->id, 'inventory_item_id' => $coffeeBeans->id, 'quantity_required' => 0.02]);
        ProductIngredient::create(['product_id' => $product->id, 'inventory_item_id' => $milk->id, 'quantity_required' => 0.25]);

        $order = Order::factory()->draft()->create(['user_id' => $user->id]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 4,
            'unit_price' => 5,
            'total_price' => 20,
        ]);

        $order = app(ProcessPaymentAction::class)->execute(
            order: $order,
            amountPaid: 20,
            paymentMethod: 'cash',
            user: $user,
        );

        $order = app(CompleteOrderAction::class)->execute($order, $user);

        $this->assertEquals(9.92, $coffeeBeans->fresh()->quantity);
        $this->assertEquals(19, $milk->fresh()->quantity);
    }

    public function test_product_without_ingredients_skips_deduction(): void
    {
        $user = User::factory()->create();

        $product = Product::factory()->create([
            'branch_id' => null,
            'stock_quantity' => 100,
        ]);

        $order = Order::factory()->draft()->create(['user_id' => $user->id]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'unit_price' => $product->price,
            'total_price' => $product->price * 2,
        ]);

        $order = app(ProcessPaymentAction::class)->execute(
            order: $order,
            amountPaid: 100,
            paymentMethod: 'cash',
            user: $user,
        );

        app(CompleteOrderAction::class)->execute($order, $user);

        $this->assertEmpty(InventoryItem::all());
    }

    public function test_insufficient_inventory_throws_exception(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Insufficient Coffee Beans');

        $branch = $this->createBranch();
        $user = User::factory()->create();

        $inventoryItem = InventoryItem::create([
            'branch_id' => $branch->id, 'name' => 'Coffee Beans', 'unit' => 'kg', 'quantity' => 0.5, 'minimum_quantity' => 0.1, 'cost_per_unit' => 15,
        ]);

        $product = Product::factory()->create([
            'branch_id' => null,
            'stock_quantity' => 100,
        ]);

        ProductIngredient::create([
            'product_id' => $product->id,
            'inventory_item_id' => $inventoryItem->id,
            'quantity_required' => 1,
        ]);

        $order = Order::factory()->draft()->create(['user_id' => $user->id]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 1,
            'unit_price' => $product->price,
            'total_price' => $product->price,
        ]);

        $order = app(ProcessPaymentAction::class)->execute(
            order: $order,
            amountPaid: 100,
            paymentMethod: 'cash',
            user: $user,
        );

        app(CompleteOrderAction::class)->execute($order, $user);
    }

    public function test_deduct_inventory_action_handles_deleted_product(): void
    {
        $branch = $this->createBranch();
        $user = User::factory()->create();

        $inventoryItem = InventoryItem::create([
            'branch_id' => $branch->id, 'name' => 'Sugar', 'unit' => 'kg', 'quantity' => 5, 'minimum_quantity' => 1, 'cost_per_unit' => 1.8,
        ]);

        $product = Product::factory()->create([
            'branch_id' => null,
            'stock_quantity' => 100,
        ]);

        ProductIngredient::create([
            'product_id' => $product->id,
            'inventory_item_id' => $inventoryItem->id,
            'quantity_required' => 0.1,
        ]);

        $order = Order::factory()->draft()->create(['user_id' => $user->id]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'unit_price' => $product->price,
            'total_price' => $product->price * 2,
        ]);

        $order->items()->delete();
        $product->delete();

        app(DeductInventoryAction::class)->execute($order);

        $this->assertEquals(5, $inventoryItem->fresh()->quantity);
    }

    public function test_refund_without_movements_does_not_error(): void
    {
        $this->seed(RoleSeeder::class);
        $user = User::factory()->create();
        $user->assignRole('admin');

        $order = Order::factory()->paid()->create([
            'total' => 10,
            'amount_paid' => 10,
            'user_id' => $user->id,
        ]);

        $order->payments()->create([
            'provider_code' => 'cash',
            'method' => 'cash',
            'amount' => 10,
            'currency' => 'USD',
            'status' => 'paid',
        ]);

        $order = app(RefundOrderAction::class)->execute($order, $user, 'Test refund without inventory');

        $this->assertEquals(OrderStatus::Refunded, $order->status);
    }

    public function test_multiple_items_deduct_inventory_correctly(): void
    {
        $branch = $this->createBranch();
        $user = User::factory()->create();

        $milk = InventoryItem::create([
            'branch_id' => $branch->id, 'name' => 'Milk', 'unit' => 'L', 'quantity' => 10, 'minimum_quantity' => 2, 'cost_per_unit' => 2.5,
        ]);

        $espresso = InventoryItem::create([
            'branch_id' => $branch->id, 'name' => 'Coffee Beans', 'unit' => 'kg', 'quantity' => 5, 'minimum_quantity' => 1, 'cost_per_unit' => 15,
        ]);

        $latte = Product::factory()->create(['branch_id' => null, 'stock_quantity' => 100]);
        ProductIngredient::create(['product_id' => $latte->id, 'inventory_item_id' => $milk->id, 'quantity_required' => 0.25]);
        ProductIngredient::create(['product_id' => $latte->id, 'inventory_item_id' => $espresso->id, 'quantity_required' => 0.02]);

        $espressoDrink = Product::factory()->create(['branch_id' => null, 'stock_quantity' => 100]);
        ProductIngredient::create(['product_id' => $espressoDrink->id, 'inventory_item_id' => $espresso->id, 'quantity_required' => 0.02]);

        $order = Order::factory()->draft()->create(['user_id' => $user->id]);

        $order->items()->create([
            'product_id' => $latte->id, 'product_name' => $latte->name,
            'quantity' => 2, 'unit_price' => 5.5, 'total_price' => 11,
        ]);

        $order->items()->create([
            'product_id' => $espressoDrink->id, 'product_name' => $espressoDrink->name,
            'quantity' => 1, 'unit_price' => 3.5, 'total_price' => 3.5,
        ]);

        $order = app(ProcessPaymentAction::class)->execute(
            order: $order,
            amountPaid: 20,
            paymentMethod: 'cash',
            user: $user,
        );

        $order = app(CompleteOrderAction::class)->execute($order, $user);

        $this->assertEquals(9.5, $milk->fresh()->quantity);
        $this->assertEquals(4.94, $espresso->fresh()->quantity);
    }
}
