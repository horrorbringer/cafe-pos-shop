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

class BakongProvider implements PaymentProvider
{
    protected string $baseUrl;

    protected string $accessToken;

    protected string $merchantId;

    public function __construct()
    {
        $this->baseUrl = config('payment.providers.bakong.base_url', 'https://api.bakong.gov.kh');
        $this->accessToken = config('payment.providers.bakong.access_token', '');
        $this->merchantId = config('payment.providers.bakong.merchant_id', '');
    }

    public function createQr(PaymentRequest $request): PaymentQr
    {
        $request->validate();

        $payload = [
            'merchantId' => $this->merchantId,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'orderReference' => $request->orderNumber,
            'description' => $request->description ?? "Order {$request->orderNumber}",
            'expiry' => now()->addMinutes($request->expiryMinutes ?? 15)->format('Y-m-d\TH:i:s'),
        ];

        if ($request->callbackUrl) {
            $payload['callbackUrl'] = $request->callbackUrl;
        }

        $response = Http::withToken($this->accessToken)
            ->timeout(30)
            ->post("{$this->baseUrl}/kak/api/dynamic-qr/v1/generate", $payload);

        if ($response->failed()) {
            Log::error('Bakong QR generation failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \RuntimeException('Failed to generate KHQR code from Bakong');
        }

        $data = $response->json('data', $response->json());

        return new PaymentQr(
            providerReference: $data['referenceId'] ?? $data['qrId'] ?? uniq('bakong_'),
            qrData: $data['qr'] ?? $data['qrData'] ?? '',
            qrImageUrl: $data['qrImageUrl'] ?? '',
            amount: $request->amount,
            currency: $request->currency,
            expiresAt: now()->addMinutes($request->expiryMinutes ?? 15),
            rawPayload: $data,
        );
    }

    public function checkStatus(string $providerReference): PaymentStatus
    {
        $response = Http::withToken($this->accessToken)
            ->timeout(30)
            ->get("{$this->baseUrl}/kak/api/dynamic-qr/v1/check-status/{$providerReference}");

        if ($response->failed()) {
            Log::error('Bakong status check failed', [
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

        $status = match ($data['status'] ?? 'PENDING') {
            'PAID', 'SUCCESS' => PaymentStatusType::Paid,
            'EXPIRED' => PaymentStatusType::Expired,
            'FAILED', 'REJECTED' => PaymentStatusType::Failed,
            default => PaymentStatusType::Pending,
        };

        return new PaymentStatus(
            status: $status,
            providerReference: $providerReference,
            transactionReference: $data['transactionId'] ?? null,
            amount: $data['amount'] ?? null,
            currency: $data['currency'] ?? null,
            message: $data['message'] ?? null,
            paidAt: isset($data['paidAt']) ? Carbon::parse($data['paidAt']) : null,
            rawPayload: $data,
        );
    }

    public function verifyWebhook(array $payload, array $headers): PaymentStatus
    {
        // Verify webhook signature if provided
        // For Bakong, verify the payload contains required fields
        $requiredFields = ['referenceId', 'status'];

        foreach ($requiredFields as $field) {
            if (! isset($payload[$field])) {
                throw new \InvalidArgumentException("Missing required webhook field: {$field}");
            }
        }

        $status = match ($payload['status']) {
            'PAID', 'SUCCESS' => PaymentStatusType::Paid,
            'EXPIRED' => PaymentStatusType::Expired,
            'FAILED', 'REJECTED' => PaymentStatusType::Failed,
            default => PaymentStatusType::Pending,
        };

        return new PaymentStatus(
            status: $status,
            providerReference: $payload['referenceId'],
            transactionReference: $payload['transactionId'] ?? null,
            amount: $payload['amount'] ?? null,
            currency: $payload['currency'] ?? null,
            message: $payload['message'] ?? null,
            paidAt: isset($payload['paidAt']) ? Carbon::parse($payload['paidAt']) : null,
            rawPayload: $payload,
        );
    }

    public function getCode(): string
    {
        return 'bakong';
    }

    public function isAvailable(): bool
    {
        return ! empty($this->accessToken) && ! empty($this->merchantId);
    }
}
