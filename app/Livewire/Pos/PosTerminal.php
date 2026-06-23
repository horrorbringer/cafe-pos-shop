<?php

namespace App\Livewire\Pos;

use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Product;
use App\Domain\Ordering\Actions\AddOrderItemAction;
use App\Domain\Ordering\Actions\CancelOrderAction;
use App\Domain\Ordering\Actions\CompleteOrderAction;
use App\Domain\Ordering\Actions\CreateOrderAction;
use App\Domain\Ordering\Actions\ProcessPaymentAction;
use App\Domain\Ordering\Actions\RecalculateOrderTotalsAction;
use App\Domain\Ordering\Actions\RemoveOrderItemAction;
use App\Domain\Ordering\Models\Order;
use App\Domain\Payment\PaymentManager;
use App\Domain\Shared\Enums\OrderStatus;
use App\Services\ReceiptPrinterService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class PosTerminal extends Component
{
    public ?Order $order = null;

    public int $selectedCategoryId = 0;

    public string $search = '';

    public float $amountTendered = 0;

    public bool $showPaymentModal = false;

    public bool $showReceiptModal = false;

    public bool $showKhqrModal = false;

    public bool $showModifierModal = false;

    public string $receiptContent = '';

    public string $paymentMethod = 'cash';

    public ?array $khqrData = null;

    public int $qrExpirySeconds = 0;

    public string $orderType = 'takeaway';

    public ?string $tableNumber = null;

    public ?array $selectedProduct = null;

    public ?array $selectedVariant = null;

    public array $selectedModifiers = [];

    public int $itemQuantity = 1;

    public string $itemNotes = '';

    public string $orderNotes = '';

    public bool $showSuspendedOrders = false;

    public bool $processing = false;

    protected ReceiptPrinterService $receiptService;

    protected PaymentManager $paymentManager;

    protected ProcessPaymentAction $processPaymentAction;

    protected RecalculateOrderTotalsAction $recalculateOrder;

    public function boot(
        ReceiptPrinterService $receiptService,
        PaymentManager $paymentManager,
        ProcessPaymentAction $processPaymentAction,
        RecalculateOrderTotalsAction $recalculateOrder,
    ): void {
        $this->receiptService = $receiptService;
        $this->paymentManager = $paymentManager;
        $this->processPaymentAction = $processPaymentAction;
        $this->recalculateOrder = $recalculateOrder;
    }

    public function mount(): void
    {
        $this->startNewOrder();
    }

    public function startNewOrder(): void
    {
        $this->order = app(CreateOrderAction::class)->execute(
            Auth::user(),
            null,
            $this->orderType,
            $this->tableNumber
        );
        $this->amountTendered = 0;
        $this->showPaymentModal = false;
        $this->showReceiptModal = false;
        $this->showKhqrModal = false;
        $this->showModifierModal = false;
        $this->receiptContent = '';
        $this->khqrData = null;
        $this->qrExpirySeconds = 0;
        $this->selectedProduct = null;
        $this->selectedVariant = null;
        $this->selectedModifiers = [];
        $this->itemQuantity = 1;
        $this->itemNotes = '';
        $this->orderNotes = '';
    }

    public function updatedOrderType(string $value): void
    {
        if ($this->order) {
            if ($value === 'dine_in' && empty($this->tableNumber)) {
                $this->dispatch('show-toast', message: 'Table number required for dine-in', type: 'error');
                $this->orderType = 'takeaway';

                return;
            }
            $this->order->update(['order_type' => $value]);
        }
    }

    public function updatedTableNumber(?string $value): void
    {
        if ($this->order) {
            $this->order->update(['table_number' => $value]);
        }
    }

    #[Computed]
    public function categories(): array
    {
        return Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->toArray();
    }

    #[Computed]
    public function products(): array
    {
        $query = Product::where('is_available', true)
            ->with(['category', 'variants', 'modifierGroups.options']);

        if ($this->selectedCategoryId > 0) {
            $query->where('category_id', $this->selectedCategoryId);
        }

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%");
        }

        return $query->get()->toArray();
    }

    #[Computed]
    public function cartItems(): array
    {
        if (! $this->order) {
            return [];
        }

        return $this->order->items()
            ->with('modifiers')
            ->get()
            ->toArray();
    }

    #[Computed]
    public function subtotal(): float
    {
        return $this->order?->subtotal ?? 0;
    }

    #[Computed]
    public function tax(): float
    {
        return $this->order?->tax ?? 0;
    }

    #[Computed]
    public function total(): float
    {
        return $this->order?->total ?? 0;
    }

    #[Computed]
    public function itemCount(): int
    {
        return $this->order?->items()->sum('quantity') ?? 0;
    }

    #[Computed]
    public function isKhqrAvailable(): bool
    {
        return $this->paymentManager->isKhqrAvailable();
    }

    #[Computed]
    public function suspendedOrders(): array
    {
        return Order::where('user_id', Auth::id())
            ->whereIn('status', [OrderStatus::Draft])
            ->where('id', '!=', $this->order?->id)
            ->withCount('items')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->toArray();
    }

    public function holdOrder(): void
    {
        if ($this->itemCount === 0) {
            $this->dispatch('show-toast', message: 'Cart is empty', type: 'error');

            return;
        }

        $this->order->update(['notes' => $this->orderNotes ?: null]);
        $this->dispatch('show-toast', message: 'Order held', type: 'success');
        $this->startNewOrder();
    }

    public function resumeOrder(int $orderId): void
    {
        $order = Order::findOrFail($orderId);

        if ($this->order && $this->itemCount > 0) {
            app(CancelOrderAction::class)->execute($this->order, Auth::user(), 'Cancelled to resume held order');
        }

        $this->order = $order;
        $this->orderNotes = $order->notes ?? '';
        $this->orderType = $order->order_type->value;
        $this->tableNumber = $order->table_number;
        $this->showSuspendedOrders = false;

        $this->dispatch('show-toast', message: 'Order resumed', type: 'success');
    }

    public function openModifierModal(int $productId): void
    {
        $product = Product::with(['variants', 'modifierGroups.options'])->findOrFail($productId);

        if ($product->stock_quantity <= 0) {
            $this->dispatch('show-toast', message: 'Product is out of stock', type: 'error');

            return;
        }

        $this->selectedProduct = $product->toArray();
        $this->selectedVariant = null;
        $this->selectedModifiers = [];
        $this->itemQuantity = 1;
        $this->itemNotes = '';
        $this->showModifierModal = true;
    }

    public function selectVariant(int $variantId): void
    {
        if (! $this->selectedProduct) {
            return;
        }

        $variants = collect($this->selectedProduct['variants'] ?? []);
        $this->selectedVariant = $variants->firstWhere('id', $variantId);
    }

    public function toggleModifier(int $modifierOptionId): void
    {
        if (! $this->selectedProduct) {
            return;
        }

        $option = null;
        $group = null;
        foreach ($this->selectedProduct['modifier_groups'] ?? [] as $g) {
            foreach ($g['options'] ?? [] as $o) {
                if ($o['id'] === $modifierOptionId) {
                    $option = $o;
                    $group = $g;
                    break 2;
                }
            }
        }

        if (! $option || ! $group) {
            return;
        }

        $existingIndex = array_search($modifierOptionId, array_column($this->selectedModifiers, 'modifier_option_id'));

        if ($existingIndex !== false) {
            unset($this->selectedModifiers[$existingIndex]);
            $this->selectedModifiers = array_values($this->selectedModifiers);
        } else {
            $groupModifiers = array_filter(
                $this->selectedModifiers,
                fn ($m) => $m['modifier_group_name'] === $group['name']
            );

            if (count($groupModifiers) >= $group['max_selections']) {
                $oldestKey = array_key_first($groupModifiers);
                if ($oldestKey !== null) {
                    unset($this->selectedModifiers[$oldestKey]);
                    $this->selectedModifiers = array_values($this->selectedModifiers);
                }
            }

            $this->selectedModifiers[] = [
                'modifier_option_id' => $modifierOptionId,
                'modifier_group_name' => $group['name'],
                'modifier_option_name' => $option['name'],
                'price' => $option['price'],
            ];
        }
    }

    public function confirmAddItem(): void
    {
        if (! $this->selectedProduct) {
            return;
        }

        $product = Product::with(['variants'])->findOrFail($this->selectedProduct['id']);

        $variant = null;
        if ($this->selectedVariant) {
            $variant = $product->variants()->find($this->selectedVariant['id']);
        }

        app(AddOrderItemAction::class)->execute(
            order: $this->order,
            product: $product,
            quantity: $this->itemQuantity,
            variant: $variant,
            modifiers: $this->selectedModifiers,
            notes: $this->itemNotes ?: null,
        );

        $this->order = $this->order->fresh();
        $this->showModifierModal = false;
        $this->selectedProduct = null;
    }

    public function addItemQuick(int $productId): void
    {
        $product = Product::with(['variants', 'modifierGroups.options'])->findOrFail($productId);

        if ($product->stock_quantity <= 0) {
            $this->dispatch('show-toast', message: 'Product is out of stock', type: 'error');

            return;
        }

        if ($product->hasVariants() || $product->modifierGroups->isNotEmpty()) {
            $this->openModifierModal($productId);

            return;
        }

        app(AddOrderItemAction::class)->execute($this->order, $product);

        $this->order = $this->order->fresh();
    }

    public function removeItem(int $itemId): void
    {
        $item = $this->order->items()->findOrFail($itemId);

        app(RemoveOrderItemAction::class)->execute($this->order, $item);

        $this->order = $this->order->fresh();
    }

    public function updateQuantity(int $itemId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeItem($itemId);

            return;
        }

        $item = $this->order->items()->findOrFail($itemId);
        $item->update([
            'quantity' => $quantity,
            'total_price' => $quantity * $item->unit_price,
        ]);

        $this->order = $this->order->fresh();
        $this->recalculateOrder->execute($this->order);
    }

    public function applyDiscount(float $discount): void
    {
        $this->order->update(['discount' => $discount]);
        $this->order = $this->order->fresh();
        $this->recalculateOrder->execute($this->order);
    }

    public function applyDiscountPercent(int $percent): void
    {
        $subtotal = $this->order->items->sum('total_price');
        $discount = round($subtotal * ($percent / 100), 2);

        $this->applyDiscount($discount);
    }

    public function isActiveDiscountPercent(int $percent): bool
    {
        if (! $this->order || $this->order->discount <= 0 || $this->subtotal <= 0) {
            return false;
        }

        return round(($this->order->discount / $this->subtotal) * 100) === $percent;
    }

    public function updatedOrderNotes(string $value): void
    {
        if ($this->order) {
            $this->order->update(['notes' => $value ?: null]);
        }
    }

    public function openPaymentModal(): void
    {
        if ($this->itemCount === 0) {
            $this->dispatch('show-toast', message: 'Cart is empty', type: 'error');

            return;
        }

        $this->showPaymentModal = true;
    }

    public function selectPaymentMethod(string $method): void
    {
        $this->paymentMethod = $method;

        if ($method === 'khqr' && $this->isKhqrAvailable) {
            $this->generateKhqr();
        }
    }

    public function generateKhqr(): void
    {
        if (! $this->order) {
            return;
        }

        $this->processing = true;

        $qrData = $this->processPaymentAction->createKhqrPayment($this->order, Auth::user());

        // Refresh order so in-memory status reflects DB transition (Draft → Pending)
        $this->order = $this->order->fresh();

        if ($qrData) {
            $this->khqrData = $qrData;
            $this->qrExpirySeconds = $qrData['seconds_until_expiry'] ?? 900;
            $this->showKhqrModal = true;

            $this->dispatch('start-qr-timer', seconds: $this->qrExpirySeconds);
        } else {
            $this->dispatch('show-toast', message: 'Failed to generate KHQR code', type: 'error');
        }

        $this->processing = false;
    }

    public function checkKhqrStatus(): void
    {
        if (! $this->khqrData || ! $this->order) {
            return;
        }

        $this->processing = true;

        try {
            $status = $this->paymentManager->checkStatus(
                providerReference: $this->khqrData['provider_reference']
            );

            if ($status->isPaid()) {
                $order = $this->processPaymentAction->confirmPayment(
                    order: $this->order,
                    providerReference: $this->khqrData['provider_reference'],
                    transactionReference: $status->transactionReference,
                    paidAt: $status->paidAt,
                    user: Auth::user()
                );

                $order = app(CompleteOrderAction::class)->execute($order, Auth::user());

                $this->receiptContent = $this->receiptService->generateReceiptContent($order);
                $this->showKhqrModal = false;
                $this->showPaymentModal = false;
                $this->showReceiptModal = true;

                $this->dispatch('show-toast', message: 'Payment confirmed!', type: 'success');
            }

            $this->processing = false;
        } catch (\Exception $e) {
            $this->dispatch('show-toast', message: 'Error checking payment status', type: 'error');
            $this->processing = false;
        }
    }

    public function processPayment(): void
    {
        if ($this->paymentMethod === 'cash') {
            if ($this->amountTendered < $this->total) {
                $this->dispatch('show-toast', message: 'Insufficient payment amount', type: 'error');

                return;
            }

            $this->processing = true;

            $order = $this->processPaymentAction->execute(
                order: $this->order,
                amountPaid: $this->amountTendered,
                paymentMethod: $this->paymentMethod,
                user: Auth::user()
            );

            $order = app(CompleteOrderAction::class)->execute($order, Auth::user());

            $this->receiptContent = $this->receiptService->generateReceiptContent($order);
            $this->showPaymentModal = false;
            $this->showReceiptModal = true;

            $this->processing = false;

            return;
        }

        $this->dispatch('show-toast', message: __('This payment method is not available'), type: 'error');
    }

    public function cancelKhqr(): void
    {
        $this->showKhqrModal = false;
        $this->khqrData = null;
    }

    public function cancelModifierModal(): void
    {
        $this->showModifierModal = false;
        $this->selectedProduct = null;
    }

    public function printReceipt(): void
    {
        $order = Order::findOrFail($this->order->id);
        $this->receiptService->print($order);

        $this->dispatch('show-toast', message: 'Receipt sent to printer', type: 'success');
    }

    public function newOrder(): void
    {
        $this->startNewOrder();
    }

    public function cancelOrder(): void
    {
        if ($this->order && $this->itemCount > 0) {
            app(CancelOrderAction::class)->execute($this->order, Auth::user(), 'Cancelled from POS');
        }

        $this->startNewOrder();
    }

    public function render()
    {
        return view('livewire.pos.pos-terminal');
    }
}
