<?php

namespace App\Domain\Payment\Providers;

use App\Domain\Payment\Contracts\PaymentProvider;
use App\Domain\Payment\Data\PaymentQr;
use App\Domain\Payment\Data\PaymentRequest;
use App\Domain\Payment\Data\PaymentStatus;
use App\Domain\Payment\Data\PaymentStatusType;
use App\Domain\Shop\Models\Setting;
use Carbon\Carbon;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use KHQR\BakongKHQR;
use KHQR\Helpers\KHQRData;
use KHQR\Helpers\Utils;
use KHQR\Models\IndividualInfo;

class BakongProvider implements PaymentProvider
{
    protected string $baseUrl;

    protected string $accessToken;

    protected string $merchantId;

    protected string $acquiringBank;

    protected string $mobileNumber;

    public function __construct()
    {
        $this->baseUrl = config('payment.providers.bakong.base_url', 'https://api-bakong.nbc.gov.kh');
        $this->accessToken = config('payment.providers.bakong.access_token', '');
        $this->merchantId = config('payment.providers.bakong.merchant_id', '');
        $this->acquiringBank = config('payment.providers.bakong.acquiring_bank', 'NBC');
        $this->mobileNumber = config('payment.providers.bakong.mobile_number', '');
    }

    public function createQr(PaymentRequest $request): PaymentQr
    {
        $request->validate();

        // Generate real KHQR locally using the SDK (no API call needed)
        try {
            $shopName = Setting::getValue('shop_name') ?: config('app.name', 'POS Cafe');
            $shopAddress = Setting::getValue('shop_address') ?: 'Phnom Penh';
            $bakongAccountId = ! empty($this->merchantId) ? $this->merchantId : 'dev@nbc';
            $acquiringBank = ! empty($this->acquiringBank) ? $this->acquiringBank : 'NBC';

            $currencyMap = [
                'KHR' => KHQRData::CURRENCY_KHR,
                'USD' => KHQRData::CURRENCY_USD,
            ];

            $individualInfo = IndividualInfo::withOptionalArray(
                bakongAccountID: $bakongAccountId,
                merchantName: $shopName,
                merchantCity: $shopAddress,
                optionalData: [
                    'acquiringBank' => $acquiringBank,
                    'currency' => $currencyMap[$request->currency] ?? KHQRData::CURRENCY_USD,
                    'amount' => $request->amount,
                    'billNumber' => $request->orderNumber,
                    'mobileNumber' => $this->mobileNumber ?: null,
                ],
            );

            $response = BakongKHQR::generateIndividual($individualInfo);
            $qrString = $response->data['qr'] ?? '';
            $md5Hash = $response->data['md5'] ?? '';

            if ($qrString !== '' && $md5Hash !== '') {
                $expiryMinutes = $request->expiryMinutes ?? 15;
                $qrString = $this->injectExpirationTimestamp($qrString, $expiryMinutes);
                $base64Png = $this->renderQrImage($qrString);

                $isDev = empty($this->merchantId);
                $newMd5 = md5($qrString);

                return new PaymentQr(
                    providerReference: $isDev ? 'dev_'.now()->timestamp.'_'.uniqid() : $newMd5,
                    qrData: $base64Png,
                    qrImageUrl: '',
                    amount: $request->amount,
                    currency: $request->currency,
                    expiresAt: now()->addMinutes($expiryMinutes),
                    rawPayload: $isDev
                        ? ['mock' => true, 'md5' => $newMd5, 'qr_emv' => $qrString]
                        : ['md5' => $newMd5, 'qr_emv' => $qrString],
                );
            }

            Log::warning('Local KHQR generation returned empty data');
        } catch (\Exception $e) {
            Log::warning('Local KHQR generation failed, falling back', [
                'error' => $e->getMessage(),
            ]);
        }

        throw new \RuntimeException('Failed to generate KHQR code');
    }

    public function checkStatus(string $providerReference): PaymentStatus
    {
        if (str_starts_with($providerReference, 'dev_')) {
            $parts = explode('_', $providerReference);
            $createdAt = isset($parts[1]) ? (int) $parts[1] : 0;
            $elapsed = now()->diffInSeconds(now()->setTimestamp($createdAt));
            $isReady = $createdAt > 0 && $elapsed >= 8;

            return new PaymentStatus(
                status: $isReady ? PaymentStatusType::Paid : PaymentStatusType::Pending,
                providerReference: $providerReference,
                transactionReference: $isReady ? 'dev_txn_'.uniqid() : null,
                amount: null,
                currency: null,
                message: $isReady ? 'Development mock payment completed' : 'Waiting for payment...',
                paidAt: $isReady ? now() : null,
                rawPayload: ['mock' => true, 'created_at' => $createdAt],
            );
        }

        // For locally generated KHQR, try the MD5-based status endpoint first.
        // If the API returns Paid/Expired/Failed, return immediately.
        // If Pending, fall through to the DB-based auto-confirm fallback.
        if (strlen($providerReference) === 32 && ctype_xdigit($providerReference)) {
            try {
                $isSandbox = str_contains($this->baseUrl, 'sit-');
                $bakongKHQR = new BakongKHQR($this->accessToken);
                $result = $bakongKHQR->checkTransactionByMD5($providerReference, $isSandbox);

                $data = $result['data'] ?? $result;

                $status = match ($data['status'] ?? 'PENDING') {
                    'PAID', 'SUCCESS' => PaymentStatusType::Paid,
                    'EXPIRED' => PaymentStatusType::Expired,
                    'FAILED', 'REJECTED' => PaymentStatusType::Failed,
                    default => PaymentStatusType::Pending,
                };

                if ($status !== PaymentStatusType::Pending) {
                    return new PaymentStatus(
                        status: $status,
                        providerReference: $providerReference,
                        transactionReference: $data['transactionId'] ?? $data['referenceId'] ?? null,
                        amount: isset($data['amount']) ? (float) $data['amount'] : null,
                        currency: $data['currency'] ?? null,
                        message: $data['message'] ?? ($result['message'] ?? null),
                        paidAt: isset($data['paidAt']) ? Carbon::parse($data['paidAt']) : null,
                        rawPayload: $data,
                    );
                }
            } catch (\Exception $e) {
                Log::warning('Bakong MD5 status check failed, trying legacy endpoint', [
                    'reference' => $providerReference,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Fallback to legacy GET endpoint
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

                if ($status !== PaymentStatusType::Pending) {
                    return new PaymentStatus(
                        status: $status,
                        providerReference: $providerReference,
                        transactionReference: $data['transactionId'] ?? null,
                        amount: isset($data['amount']) ? (float) $data['amount'] : null,
                        currency: $data['currency'] ?? null,
                        message: $data['message'] ?? null,
                        paidAt: isset($data['paidAt']) ? Carbon::parse($data['paidAt']) : null,
                        rawPayload: $data,
                    );
                }

                Log::info('Bakong status check returned pending', [
                    'reference' => $providerReference,
                ]);
            } else {
                Log::warning('Bakong status check API error', [
                    'reference' => $providerReference,
                    'status' => $response->status(),
                ]);
            }
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

    private function injectExpirationTimestamp(string $qrString, int $expiryMinutes = 15): string
    {
        $qrNoCrc = substr($qrString, 0, -8);
        $pos = 0;
        $before99 = '';
        $tag99Value = '';
        $after99 = '';

        while ($pos < strlen($qrNoCrc)) {
            $tag = substr($qrNoCrc, $pos, 2);
            $len = (int) substr($qrNoCrc, $pos + 2, 2);
            $value = substr($qrNoCrc, $pos + 4, $len);
            $nextPos = $pos + 4 + $len;

            if ($tag === '99') {
                $before99 = substr($qrNoCrc, 0, $pos);
                $tag99Value = $value;
                $after99 = substr($qrNoCrc, $nextPos);
                break;
            }

            $pos = $nextPos;
        }

        if ($tag99Value === '') {
            return $qrString;
        }

        $expirationMs = (string) floor(now()->addMinutes($expiryMinutes)->timestamp * 1000);
        $tag99Value .= '01'.str_pad((string) strlen($expirationMs), 2, '0', STR_PAD_LEFT).$expirationMs;
        $newQrNoCrc = $before99.'99'.str_pad((string) strlen($tag99Value), 2, '0', STR_PAD_LEFT).$tag99Value.$after99;

        return $newQrNoCrc.'6304'.Utils::crc16($newQrNoCrc.'6304');
    }

    private function renderQrImage(string $data): string
    {
        $options = new QROptions;
        $options->outputType = QROutputInterface::GDIMAGE_PNG;
        $options->scale = 5;
        $options->outputBase64 = false;

        return base64_encode((new QRCode($options))->render($data));
    }
}
