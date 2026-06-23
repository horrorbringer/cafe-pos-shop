<?php

namespace App\Domain\Payment\Providers;

use App\Domain\Payment\Contracts\PaymentProvider;
use App\Domain\Payment\Data\PaymentQr;
use App\Domain\Payment\Data\PaymentRequest;
use App\Domain\Payment\Data\PaymentStatus;
use App\Domain\Payment\Data\PaymentStatusType;

class CashProvider implements PaymentProvider
{
    public function createQr(PaymentRequest $request): PaymentQr
    {
        throw new \LogicException('Cash payments do not require QR generation');
    }

    public function checkStatus(string $providerReference): PaymentStatus
    {
        // Cash payments are confirmed immediately by the cashier
        return new PaymentStatus(
            status: PaymentStatusType::Paid,
            providerReference: $providerReference,
            message: 'Cash payment confirmed by cashier',
            paidAt: now(),
        );
    }

    public function verifyWebhook(array $payload, array $headers): PaymentStatus
    {
        throw new \LogicException('Cash payments do not have webhooks');
    }

    public function getCode(): string
    {
        return 'cash';
    }

    public function isAvailable(): bool
    {
        return true;
    }
}
