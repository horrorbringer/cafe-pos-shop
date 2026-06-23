<?php

namespace App\Domain\Payment\Data;

readonly class PaymentQr
{
    public function __construct(
        public string $providerReference,
        public string $qrData,
        public string $qrImageUrl,
        public float $amount,
        public string $currency,
        public \DateTime $expiresAt,
        public array $rawPayload = [],
    ) {}

    public function isExpired(): bool
    {
        return now()->greaterThan($this->expiresAt);
    }

    public function secondsUntilExpiry(): int
    {
        return max(0, now()->diffInSeconds($this->expiresAt, false));
    }

    public function toArray(): array
    {
        return [
            'provider_reference' => $this->providerReference,
            'qr_data' => $this->qrData,
            'qr_image_url' => $this->qrImageUrl,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'expires_at' => $this->expiresAt->toIso8601String(),
            'raw_payload' => $this->rawPayload,
        ];
    }
}
