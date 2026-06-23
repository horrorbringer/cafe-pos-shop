<?php

namespace App\Domain\Payment\Providers;

use App\Domain\Payment\Contracts\PaymentProvider;
use App\Domain\Payment\Data\PaymentQr;
use App\Domain\Payment\Data\PaymentRequest;
use App\Domain\Payment\Data\PaymentStatus;
use App\Domain\Payment\Data\PaymentStatusType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ThirdPartyKhqrProvider implements PaymentProvider
{
    protected string $baseUrl;

    protected string $apiKey;

    protected string $merchantId;

    public function __construct()
    {
        $this->baseUrl = config('payment.providers.third_party_khqr.base_url', '');
        $this->apiKey = config('payment.providers.third_party_khqr.api_key', '');
        $this->merchantId = config('payment.providers.third_party_khqr.merchant_id', '');
    }

    public function createQr(PaymentRequest $request): PaymentQr
    {
        $request->validate();

        $payload = [
            'merchant_id' => $this->merchantId,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'order_reference' => $request->orderNumber,
            'description' => $request->description ?? "Order {$request->orderNumber}",
            'expiry_minutes' => $request->expiryMinutes ?? 15,
        ];

        if ($request->callbackUrl) {
            $payload['callback_url'] = $request->callbackUrl;
        }

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->timeout(30)->post("{$this->baseUrl}/api/v1/qr/generate", $payload);

        if ($response->failed()) {
            Log::error('Third-party KHQR generation failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \RuntimeException('Failed to generate KHQR code from third-party provider');
        }

        $data = $response->json('data', $response->json());

        return new PaymentQr(
            providerReference: $data['reference_id'] ?? uniq('khqr_'),
            qrData: $data['qr_data'] ?? $data['qr'] ?? '',
            qrImageUrl: $data['qr_image_url'] ?? $data['qr_url'] ?? '',
            amount: $request->amount,
            currency: $request->currency,
            expiresAt: now()->addMinutes($request->expiryMinutes ?? 15),
            rawPayload: $data,
        );
    }

    public function checkStatus(string $providerReference): PaymentStatus
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
        ])->timeout(30)->get("{$this->baseUrl}/api/v1/payment/status/{$providerReference}");

        if ($response->failed()) {
            Log::error('Third-party KHQR status check failed', [
                'reference' => $providerReference,
                'status' => $response->status(),
            ]);

            return new PaymentStatus(
                status: PaymentStatusType::Failed,
                providerReference: $providerReference,
                message: 'Failed to check payment status',
            );
        }

        $data = $response->json('data', $response->json());

        $status = match ($data['status'] ?? 'pending') {
            'paid', 'success', 'completed' => PaymentStatusType::Paid,
            'expired' => PaymentStatusType::Expired,
            'failed', 'cancelled', 'rejected' => PaymentStatusType::Failed,
            default => PaymentStatusType::Pending,
        };

        return new PaymentStatus(
            status: $status,
            providerReference: $providerReference,
            transactionReference: $data['transaction_id'] ?? null,
            amount: $data['amount'] ?? null,
            currency: $data['currency'] ?? null,
            message: $data['message'] ?? null,
            paidAt: isset($data['paid_at']) ? Carbon::parse($data['paid_at']) : null,
            rawPayload: $data,
        );
    }

    public function verifyWebhook(array $payload, array $headers): PaymentStatus
    {
        // Verify webhook signature
        $signature = $headers['x-signature'] ?? $headers['X-Signature'] ?? null;
        $expectedSignature = hash_hmac('sha256', json_encode($payload), $this->apiKey);

        if ($signature && ! hash_equals($expectedSignature, $signature)) {
            throw new \InvalidArgumentException('Invalid webhook signature');
        }

        $requiredFields = ['reference_id', 'status'];

        foreach ($requiredFields as $field) {
            if (! isset($payload[$field])) {
                throw new \InvalidArgumentException("Missing required webhook field: {$field}");
            }
        }

        $status = match ($payload['status']) {
            'paid', 'success', 'completed' => PaymentStatusType::Paid,
            'expired' => PaymentStatusType::Expired,
            'failed', 'cancelled', 'rejected' => PaymentStatusType::Failed,
            default => PaymentStatusType::Pending,
        };

        return new PaymentStatus(
            status: $status,
            providerReference: $payload['reference_id'],
            transactionReference: $payload['transaction_id'] ?? null,
            amount: $payload['amount'] ?? null,
            currency: $payload['currency'] ?? null,
            message: $payload['message'] ?? null,
            paidAt: isset($payload['paid_at']) ? Carbon::parse($payload['paid_at']) : null,
            rawPayload: $payload,
        );
    }

    public function getCode(): string
    {
        return 'third_party_khqr';
    }

    public function isAvailable(): bool
    {
        return ! empty($this->apiKey) && ! empty($this->baseUrl) && ! empty($this->merchantId);
    }
}
