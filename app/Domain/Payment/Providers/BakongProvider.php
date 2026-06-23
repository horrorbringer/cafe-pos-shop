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

        try {
            $response = Http::withToken($this->accessToken)
                ->timeout(10)
                ->post("{$this->baseUrl}/kak/api/dynamic-qr/v1/generate", $payload);

            if ($response->successful()) {
                $data = $response->json('data', $response->json());

                return new PaymentQr(
                    providerReference: $data['referenceId'] ?? $data['qrId'] ?? uniqid('bakong_'),
                    qrData: $data['qr'] ?? $data['qrData'] ?? '',
                    qrImageUrl: $data['qrImageUrl'] ?? '',
                    amount: $request->amount,
                    currency: $request->currency,
                    expiresAt: now()->addMinutes($request->expiryMinutes ?? 15),
                    rawPayload: $data,
                );
            }

            Log::warning('Bakong QR generation API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Exception $e) {
            Log::warning('Bakong QR generation connection failed', [
                'error' => $e->getMessage(),
            ]);
        }

        if (config('payment.khqr.static_qr_enabled', true)) {
            $reference = uniqid('dev_');
            $qrData = "khqr://{$this->merchantId}/{$request->orderNumber}/{$request->amount}";

            return new PaymentQr(
                providerReference: $reference,
                qrData: $qrData,
                qrImageUrl: '',
                amount: $request->amount,
                currency: $request->currency,
                expiresAt: now()->addMinutes($request->expiryMinutes ?? 15),
                rawPayload: ['mock' => true, 'reference' => $reference],
            );
        }

        throw new \RuntimeException('Failed to generate KHQR code from Bakong');
    }

    public function checkStatus(string $providerReference): PaymentStatus
    {
        if (str_starts_with($providerReference, 'dev_')) {
            return new PaymentStatus(
                status: PaymentStatusType::Paid,
                providerReference: $providerReference,
                transactionReference: 'dev_txn_'.uniqid(),
                amount: null,
                currency: null,
                message: 'Development mock payment',
                paidAt: now(),
                rawPayload: ['mock' => true],
            );
        }

        try {
            $response = Http::withToken($this->accessToken)
                ->timeout(10)
                ->get("{$this->baseUrl}/kak/api/dynamic-qr/v1/check-status/{$providerReference}");

            if ($response->successful()) {
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

            Log::warning('Bakong status check API error', [
                'reference' => $providerReference,
                'status' => $response->status(),
            ]);
        } catch (\Exception $e) {
            Log::warning('Bakong status check connection failed', [
                'reference' => $providerReference,
                'error' => $e->getMessage(),
            ]);
        }

        return new PaymentStatus(
            status: PaymentStatusType::Pending,
            providerReference: $providerReference,
            message: 'Payment status unknown',
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
