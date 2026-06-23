<?php

namespace App\Http\Controllers\Payment;

use App\Domain\Ordering\Actions\ProcessPaymentAction;
use App\Domain\Ordering\Models\Order;
use App\Domain\Payment\PaymentManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController
{
    public function __construct(
        protected PaymentManager $paymentManager,
        protected ProcessPaymentAction $processPaymentAction,
    ) {}

    public function handle(Request $request): JsonResponse
    {
        try {
            // Verify webhook signature
            $headers = $request->headers->all();
            $payload = $request->all();

            Log::info('Payment webhook received', [
                'provider' => $request->query('provider'),
                'payload_keys' => array_keys($payload),
            ]);

            // Verify and parse the webhook
            $status = $this->paymentManager->verifyWebhook(
                payload: $payload,
                headers: $headers,
                providerCode: $request->query('provider'),
            );

            if ($status->isPaid()) {
                // Find the order by provider reference
                $order = Order::where('order_number', $status->providerReference)
                    ->orWhereHas('payments', function ($query) use ($status) {
                        $query->where('provider_reference', $status->providerReference);
                    })
                    ->first();

                if (! $order) {
                    Log::warning('Order not found for payment webhook', [
                        'reference' => $status->providerReference,
                    ]);

                    return response()->json(['message' => 'Order not found'], 404);
                }

                // Confirm the payment
                $this->processPaymentAction->confirmPayment(
                    order: $order,
                    providerReference: $status->providerReference,
                    transactionReference: $status->transactionReference,
                    paidAt: $status->paidAt,
                );

                Log::info('Payment confirmed via webhook', [
                    'order' => $order->order_number,
                    'reference' => $status->providerReference,
                ]);

                return response()->json(['message' => 'Payment confirmed']);
            }

            Log::info('Webhook received but payment not successful', [
                'status' => $status->status->value,
                'reference' => $status->providerReference,
            ]);

            return response()->json(['message' => 'Payment not confirmed']);

        } catch (\InvalidArgumentException $e) {
            Log::error('Invalid webhook payload', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Invalid payload'], 400);

        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Internal error'], 500);
        }
    }

    public function checkStatus(Request $request, string $orderNumber): JsonResponse
    {
        try {
            $order = Order::where('order_number', $orderNumber)->firstOrFail();

            $pendingPayment = $order->payments()
                ->where('status', 'pending')
                ->where('method', '!=', 'cash')
                ->latest()
                ->first();

            if (! $pendingPayment) {
                return response()->json([
                    'status' => 'no_pending_payment',
                    'order_status' => $order->status->value,
                ]);
            }

            // Check status with provider
            $status = $this->paymentManager->checkStatus(
                providerReference: $pendingPayment->provider_reference,
                providerCode: $pendingPayment->provider_code,
            );

            // If paid, confirm the payment
            if ($status->isPaid()) {
                $this->processPaymentAction->confirmPayment(
                    order: $order,
                    providerReference: $pendingPayment->provider_reference,
                    transactionReference: $status->transactionReference,
                    paidAt: $status->paidAt,
                );

                return response()->json([
                    'status' => 'paid',
                    'order_status' => 'paid',
                ]);
            }

            return response()->json([
                'status' => $status->status->value,
                'order_status' => $order->status->value,
            ]);

        } catch (\Exception $e) {
            Log::error('Status check failed', [
                'order' => $orderNumber,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => 'Status check failed'], 500);
        }
    }
}
