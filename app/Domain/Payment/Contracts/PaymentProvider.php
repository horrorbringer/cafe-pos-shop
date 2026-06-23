<?php

namespace App\Domain\Payment\Contracts;

use App\Domain\Payment\Data\PaymentQr;
use App\Domain\Payment\Data\PaymentRequest;
use App\Domain\Payment\Data\PaymentStatus;

interface PaymentProvider
{
    /**
     * Create a KHQR QR code for payment.
     */
    public function createQr(PaymentRequest $request): PaymentQr;

    /**
     * Check the status of a payment by provider reference.
     */
    public function checkStatus(string $providerReference): PaymentStatus;

    /**
     * Verify and parse a webhook payload from the provider.
     */
    public function verifyWebhook(array $payload, array $headers): PaymentStatus;

    /**
     * Get the provider code identifier.
     */
    public function getCode(): string;

    /**
     * Check if the provider is currently available.
     */
    public function isAvailable(): bool;
}
