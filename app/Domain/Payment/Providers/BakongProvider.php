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
use Illuminate\Support\Facades\Log;
use KHQR\BakongKHQR;
use KHQR\Helpers\KHQRData;
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

        try {
            $shopName = Setting::getValue('shop_name') ?: config('app.name', 'POS Cafe');
            $shopAddress = Setting::getValue('shop_address') ?: 'Phnom Penh';
            $bakongAccountId = $this->merchantId ?: 'dev@nbc';
            $expiryMinutes = $request->expiryMinutes ?? 15;

            $payment = new IndividualInfo(
                bakongAccountID: $bakongAccountId,
                merchantName: $shopName,
                merchantCity: $shopAddress,
                currency: $request->currency === 'KHR' ? KHQRData::CURRENCY_KHR : KHQRData::CURRENCY_USD,
                amount: $request->amount,
                billNumber: $request->orderNumber,
                mobileNumber: $this->mobileNumber ?: null,
                acquiringBank: $this->acquiringBank ?: null,
                purposeOfTransaction: 'Checkout payment',
                expirationTimestamp: (string) floor((microtime(true) + $expiryMinutes * 60) * 1000),
            );

            $result = BakongKHQR::generateIndividual($payment);

            if ($result->status['code'] !== 0) {
                throw new \RuntimeException($result->status['message'] ?? 'Failed to generate KHQR code');
            }

            $qrString = $result->data['qr'] ?? '';
            $md5 = $result->data['md5'] ?? '';

            if ($qrString === '' || $md5 === '') {
                throw new \RuntimeException('Failed to generate KHQR code');
            }

            $base64Png = $this->renderQrImage($qrString);

            $isDev = empty($this->merchantId);

            return new PaymentQr(
                providerReference: $isDev ? 'dev_'.now()->timestamp.'_'.uniqid() : $md5,
                qrData: $base64Png,
                qrImageUrl: '',
                amount: $request->amount,
                currency: $request->currency,
                expiresAt: now()->addMinutes($expiryMinutes),
                rawPayload: $isDev
                    ? ['mock' => true, 'md5' => $md5, 'qr_emv' => $qrString]
                    : ['md5' => $md5, 'qr_emv' => $qrString],
            );
        } catch (\Exception $e) {
            Log::warning('KHQR generation failed', ['error' => $e->getMessage()]);

            throw new \RuntimeException('Failed to generate KHQR code', previous: $e);
        }
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

        // Check transaction by MD5 via Bakong API
        if (strlen($providerReference) === 32 && ctype_xdigit($providerReference)) {
            try {
                $isSandbox = str_contains($this->baseUrl, 'sit-');
                $bakong = new BakongKHQR($this->accessToken);
                $result = $bakong->checkTransactionByMD5($providerReference, $isSandbox);

                $isPaid = ($result['responseCode'] ?? null) === 0 && ! empty($result['data']);

                if ($isPaid) {
                    $data = $result['data'];

                    return new PaymentStatus(
                        status: PaymentStatusType::Paid,
                        providerReference: $providerReference,
                        transactionReference: $data['hash'] ?? null,
                        amount: isset($data['amount']) ? (float) $data['amount'] : null,
                        currency: $data['currency'] ?? null,
                        message: $result['responseMessage'] ?? 'Payment successful',
                        paidAt: isset($data['createdDateMs']) ? Carbon::parse($data['createdDateMs']) : now(),
                        rawPayload: $result,
                    );
                }

                Log::info('Bakong payment not yet confirmed', [
                    'reference' => $providerReference,
                    'responseCode' => $result['responseCode'] ?? null,
                ]);
            } catch (\Exception $e) {
                Log::warning('Bakong status check failed', [
                    'reference' => $providerReference,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return new PaymentStatus(
            status: PaymentStatusType::Pending,
            providerReference: $providerReference,
            message: 'Waiting for payment.',
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

    private function renderQrImage(string $data): string
    {
        $options = new QROptions;
        $options->outputType = QROutputInterface::GDIMAGE_PNG;
        $options->scale = 5;
        $options->outputBase64 = false;

        return base64_encode((new QRCode($options))->render($data));
    }
}
