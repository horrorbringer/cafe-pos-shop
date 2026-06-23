<?php

namespace App\Domain\Payment\Data;

readonly class PaymentStatus
{
    public function __construct(
        public PaymentStatusType $status,
        public string $providerReference,
        public ?string $transactionReference = null,
        public ?float $amount = null,
        public ?string $currency = null,
        public ?string $message = null,
        public ?\DateTime $paidAt = null,
        public array $rawPayload = [],
    ) {}

    public function isPaid(): bool
    {
        return $this->status === PaymentStatusType::Paid;
    }

    public function isPending(): bool
    {
        return $this->status === PaymentStatusType::Pending;
    }

    public function isFailed(): bool
    {
        return $this->status === PaymentStatusType::Failed;
    }

    public function isExpired(): bool
    {
        return $this->status === PaymentStatusType::Expired;
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status->value,
            'provider_reference' => $this->providerReference,
            'transaction_reference' => $this->transactionReference,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'message' => $this->message,
            'paid_at' => $this->paidAt?->toIso8601String(),
            'raw_payload' => $this->rawPayload,
        ];
    }
}
