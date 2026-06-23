<?php

namespace App\Domain\Ordering\Actions;

use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Models\ProductVariant;
use App\Domain\Ordering\Models\Order;
use App\Domain\Ordering\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class AddOrderItemAction
{
    public function execute(
        Order $order,
        Product $product,
        int $quantity = 1,
        ?ProductVariant $variant = null,
        array $modifiers = [],
        ?string $notes = null,
    ): OrderItem {
        return DB::transaction(function () use ($order, $product, $quantity, $variant, $modifiers, $notes) {
            // Validate stock
            $availableStock = $this->getAvailableStock($product, $variant);
            if ($availableStock < $quantity) {
                throw new \RuntimeException("Insufficient stock for {$product->name}. Available: {$availableStock}");
            }

            $basePrice = $product->price;
            $modifierTotal = 0;

            foreach ($modifiers as $modifierData) {
                $modifierTotal += $modifierData['price'] ?? 0;
            }

            $unitPrice = $basePrice + ($variant?->price_adjustment ?? 0) + $modifierTotal;
            $totalPrice = $unitPrice * $quantity;

            $item = $order->items()->create([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'variant_name' => $variant?->name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'notes' => $notes,
            ]);

            foreach ($modifiers as $modifierData) {
                $item->modifiers()->create([
                    'modifier_option_id' => $modifierData['modifier_option_id'],
                    'modifier_group_name' => $modifierData['modifier_group_name'],
                    'modifier_option_name' => $modifierData['modifier_option_name'],
                    'price' => $modifierData['price'],
                ]);
            }

            // Decrement product/variant stock
            $this->decrementStock($product, $variant, $quantity);

            $this->recalculateOrder($order);

            return $item;
        });
    }

    private function getAvailableStock(Product $product, ?ProductVariant $variant): int
    {
        if ($variant) {
            return $variant->stock_quantity;
        }

        return $product->stock_quantity;
    }

    private function decrementStock(Product $product, ?ProductVariant $variant, int $quantity): void
    {
        if ($variant) {
            $variant->decrement('stock_quantity', $quantity);
        } else {
            $product->decrement('stock_quantity', $quantity);
        }
    }

    private function recalculateOrder(Order $order): void
    {
        $subtotal = $order->items->sum('total_price');
        $taxRate = config('pos.tax_rate', 0.10);
        $tax = round($subtotal * $taxRate, 2);
        $total = $subtotal + $tax - $order->discount;

        $order->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => max(0, $total),
        ]);
    }
}
