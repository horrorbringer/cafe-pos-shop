<?php

namespace App\Domain\Ordering\Actions;

use App\Domain\Notifications\Events\PaymentFailed;
use App\Domain\Ordering\Models\Order;
use App\Domain\Payment\Data\PaymentRequest;
use App\Domain\Payment\PaymentManager;
use App\Domain\Shared\Enums\OrderStatus;
use App\Domain\Shared\Enums\PaymentMethod;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessPaymentAction
{
    public function __construct(
        protected PaymentManager $paymentManager,
        protected TransitionOrderStatusAction $transitionStatus,
    ) {}

    public function execute(
        Order $order,
        float $amountPaid,
        string $paymentMethod = 'cash',
        ?string $providerReference = null,
        ?string $paymentStatus = null,
        ?User $user = null,
        ?string $idempotencyKey = null,
    ): Order {
        return DB::transaction(function () use ($order, $amountPaid, $paymentMethod, $providerReference, $paymentStatus, $user, $idempotencyKey) {
            // Idempotency check
            if ($idempotencyKey !== null) {
                $existing = $order->payments()->where('idempotency_key', $idempotencyKey)->first();
                if ($existing) {
                    return $order->fresh();
                }
            }

            // State machine: Draft->Pending first if needed
            if ($order->status === OrderStatus::Draft) {
                $this->transitionStatus->execute(
                    $order,
                    OrderStatus::Pending,
                    $user?->id,
                    'Payment initiated',
                );
            }

            if ($order->status !== OrderStatus::Pending) {
                throw new \RuntimeException(
                    "Cannot process payment for order in {$order->status->value} status."
                );
            }

            // Determine provider code
            $providerCode = $this->getProviderCode($paymentMethod);

            // Create payment record
            $payment = $order->payments()->create([
                'provider_code' => $providerCode,
                'method' => $paymentMethod,
                'amount' => $amountPaid,
                'currency' => config('payment.khqr.default_currency', 'USD'),
                'status' => $paymentStatus ?? 'pending',
                'provider_reference' => $providerReference,
                'idempotency_key' => $idempotencyKey,
            ]);

            // For KHQR payments, we wait for webhook confirmation
            // For cash payments, we confirm immediately
            if ($paymentMethod === PaymentMethod::Cash->value) {
                $payment->markAsPaid();
                $this->finalizePayment($order, $payment, $user);
            } else {
                Log::info('Payment pending confirmation', [
                    'order' => $order->order_number,
                    'method' => $paymentMethod,
                    'provider' => $providerCode,
                ]);
            }

            return $order->fresh();
        });
    }

    public function confirmPayment(Order $order, string $providerReference, ?string $transactionReference = null, ?\DateTime $paidAt = null, ?User $user = null): Order
    {
        return DB::transaction(function () use ($order, $providerReference, $transactionReference, $paidAt, $user) {
            $payment = $order->payments()
                ->where('provider_reference', $providerReference)
                ->where('status', 'pending')
                ->first();

            if (! $payment) {
                event(new PaymentFailed(
                    order: $order,
                    reason: "No pending payment found for reference: {$providerReference}",
                    paymentMethod: 'khqr',
                ));

                throw new \RuntimeException("No pending payment found for reference: {$providerReference}");
            }

            $payment->markAsPaid($transactionReference, $paidAt);

            $this->finalizePayment($order, $payment, $user);

            return $order->fresh();
        });
    }

    protected function finalizePayment(Order $order, $payment, ?User $user): void
    {
        $totalPaid = $order->payments()->where('status', 'paid')->sum('amount');
        $changeAmount = max(0, $totalPaid - $order->total);

        $order->update([
            'amount_paid' => $totalPaid,
            'change_amount' => $changeAmount,
        ]);

        if ($totalPaid >= $order->total) {
            $this->transitionStatus->execute(
                $order,
                OrderStatus::Paid,
                $user?->id,
                "Paid with {$payment->method->value}. Amount: {$payment->amount}",
            );
        }
    }

    protected function getProviderCode(string $paymentMethod): string
    {
        return match ($paymentMethod) {
            'cash' => 'cash',
            'khqr', 'qr' => $this->paymentManager->getActiveProviderCode(),
            default => $paymentMethod,
        };
    }

    public function createKhqrPayment(Order $order, User $user): ?array
    {
        if (! $this->paymentManager->isKhqrAvailable()) {
            return null;
        }

        $request = new PaymentRequest(
            orderNumber: $order->order_number,
            amount: $order->total,
            currency: config('payment.khqr.default_currency', 'USD'),
            method: PaymentMethod::Qr,
            description: "Payment for order {$order->order_number}",
            expiryMinutes: config('payment.khqr.expiry_minutes', 15),
            callbackUrl: config('payment.webhook.url'),
        );

        try {
            $qr = $this->paymentManager->createQr($request);

            // Create pending payment record
            $order->payments()->create([
                'provider_code' => $this->paymentManager->getActiveProviderCode(),
                'method' => PaymentMethod::Qr->value,
                'amount' => $order->total,
                'currency' => $qr->currency,
                'status' => 'pending',
                'provider_reference' => $qr->providerReference,
            ]);

            return $qr->toArray();
        } catch (\Exception $e) {
            Log::error('Failed to create KHQR payment', [
                'order' => $order->order_number,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
