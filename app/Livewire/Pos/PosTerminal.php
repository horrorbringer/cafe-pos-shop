<?php

namespace App\Livewire\Pos;

use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\Product;
use App\Domain\Ordering\Actions\AddOrderItemAction;
use App\Domain\Ordering\Actions\CancelOrderAction;
use App\Domain\Ordering\Actions\CompleteOrderAction;
use App\Domain\Ordering\Actions\CreateOrderAction;
use App\Domain\Ordering\Actions\ProcessPaymentAction;
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

    protected ReceiptPrinterService $receiptService;

    protected PaymentManager $paymentManager;

    protected ProcessPaymentAction $processPaymentAction;

    public function boot(
        ReceiptPrinterService $receiptService,
        PaymentManager $paymentManager,
        ProcessPaymentAction $processPaymentAction
    ): void {
        $this->receiptService = $receiptService;
        $this->paymentManager = $paymentManager;
        $this->processPaymentAction = $processPaymentAction;
    }

    public function mount(): void
    {
        $this->startNewOrder();
    }

    public function startNewOrder(): void
    {
        $this->order = (new CreateOrderAction)->execute(
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
        return $this->order?->items->sum('quantity') ?? 0;
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
            (new CancelOrderAction)->execute($this->order, Auth::user(), 'Cancelled to resume held order');
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

    public function toggleModifier(int $modifierGroupId, int $modifierOptionId, string $groupName, string $optionName, float $price, int $maxSelections, bool $isRequired): void
    {
        $existingIndex = array_search($modifierOptionId, array_column($this->selectedModifiers, 'modifier_option_id'));

        if ($existingIndex !== false) {
            unset($this->selectedModifiers[$existingIndex]);
            $this->selectedModifiers = array_values($this->selectedModifiers);
        } else {
            $groupModifiers = array_filter($this->selectedModifiers, fn ($m) => $m['modifier_group_name'] === $groupName);

            if (count($groupModifiers) >= $maxSelections) {
                $oldestKey = array_key_first(array_filter(
                    $this->selectedModifiers,
                    fn ($m) => $m['modifier_group_name'] === $groupName
                ));
                if ($oldestKey !== null) {
                    unset($this->selectedModifiers[$oldestKey]);
                    $this->selectedModifiers = array_values($this->selectedModifiers);
                }
            }

            $this->selectedModifiers[] = [
                'modifier_option_id' => $modifierOptionId,
                'modifier_group_name' => $groupName,
                'modifier_option_name' => $optionName,
                'price' => $price,
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

        (new AddOrderItemAction)->execute(
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

        (new AddOrderItemAction)->execute($this->order, $product);

        $this->order = $this->order->fresh();
    }

    public function removeItem(int $itemId): void
    {
        $item = $this->order->items()->findOrFail($itemId);

        (new RemoveOrderItemAction)->execute($this->order, $item);

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

        $subtotal = $this->order->items->sum('total_price');
        $taxRate = config('pos.tax_rate', 0.10);
        $tax = round($subtotal * $taxRate, 2);

        $this->order->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal + $tax - $this->order->discount,
        ]);

        $this->order = $this->order->fresh();
    }

    public function applyDiscount(float $discount): void
    {
        $this->order->update(['discount' => $discount]);

        $subtotal = $this->order->items->sum('total_price');
        $taxRate = config('pos.tax_rate', 0.10);
        $tax = round($subtotal * $taxRate, 2);

        $this->order->update([
            'tax' => $tax,
            'total' => $subtotal + $tax - $discount,
        ]);

        $this->order = $this->order->fresh();
    }

    public function applyDiscountPercent(int $percent): void
    {
        $subtotal = $this->order->items->sum('total_price');
        $discount = round($subtotal * ($percent / 100), 2);

        $this->applyDiscount($discount);
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

        $qrData = $this->processPaymentAction->createKhqrPayment($this->order, Auth::user());

        if ($qrData) {
            $this->khqrData = $qrData;
            $this->qrExpirySeconds = $qrData['seconds_until_expiry'] ?? 900;
            $this->showKhqrModal = true;

            $this->dispatch('start-qr-timer', seconds: $this->qrExpirySeconds);
        } else {
            $this->dispatch('show-toast', message: 'Failed to generate KHQR code', type: 'error');
        }
    }

    public function checkKhqrStatus(): void
    {
        if (! $this->khqrData || ! $this->order) {
            return;
        }

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

                $order = (new CompleteOrderAction)->execute($order, Auth::user());

                $this->receiptContent = $this->receiptService->generateReceiptContent($order);
                $this->showKhqrModal = false;
                $this->showPaymentModal = false;
                $this->showReceiptModal = true;

                $this->dispatch('show-toast', message: 'Payment confirmed!', type: 'success');
            }
        } catch (\Exception $e) {
            $this->dispatch('show-toast', message: 'Error checking payment status', type: 'error');
        }
    }

    public function processPayment(): void
    {
        if ($this->paymentMethod === 'cash') {
            if ($this->amountTendered < $this->total) {
                $this->dispatch('show-toast', message: 'Insufficient payment amount', type: 'error');

                return;
            }

            $order = $this->processPaymentAction->execute(
                order: $this->order,
                amountPaid: $this->amountTendered,
                paymentMethod: $this->paymentMethod,
                user: Auth::user()
            );

            $order = (new CompleteOrderAction)->execute($order, Auth::user());

            $this->receiptContent = $this->receiptService->generateReceiptContent($order);
            $this->showPaymentModal = false;
            $this->showReceiptModal = true;
        }
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
            (new CancelOrderAction)->execute($this->order, Auth::user(), 'Cancelled from POS');
        }

        $this->startNewOrder();
    }

    public function render()
    {
        return view('livewire.pos.pos-terminal');
    }
}
