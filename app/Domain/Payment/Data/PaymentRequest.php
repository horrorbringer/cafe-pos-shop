<?php

namespace App\Domain\Payment\Data;

use App\Domain\Shared\Enums\PaymentMethod;
use Illuminate\Support\Facades\Validator;

readonly class PaymentRequest
{
    public function __construct(
        public string $orderNumber,
        public float $amount,
        public string $currency = 'USD',
        public PaymentMethod $method = PaymentMethod::Qr,
        public ?string $description = null,
        public ?int $expiryMinutes = 15,
        public ?string $callbackUrl = null,
    ) {}

    public function validate(): void
    {
        $validator = Validator::make([
            'order_number' => $this->orderNumber,
            'amount' => $this->amount,
            'currency' => $this->currency,
        ], [
            'order_number' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['required', 'string', 'in:USD,KHR'],
        ]);

        $validator->validate();
    }

    public function toArray(): array
    {
        return [
            'order_number' => $this->orderNumber,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'method' => $this->method->value,
            'description' => $this->description,
            'expiry_minutes' => $this->expiryMinutes,
            'callback_url' => $this->callbackUrl,
        ];
    }
}
