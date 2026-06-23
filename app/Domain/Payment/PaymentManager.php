<?php

namespace App\Domain\Payment;

use App\Domain\Payment\Contracts\PaymentProvider;
use App\Domain\Payment\Data\PaymentQr;
use App\Domain\Payment\Data\PaymentRequest;
use App\Domain\Payment\Data\PaymentStatus;
use App\Domain\Shop\Models\Setting;
use Illuminate\Support\Facades\Log;

class PaymentManager
{
    protected ?PaymentProvider $activeProvider = null;

    protected array $providers = [];

    public function register(string $code, PaymentProvider $provider): void
    {
        $this->providers[$code] = $provider;
    }

    public function provider(?string $code = null): PaymentProvider
    {
        $code = $code ?? $this->getActiveProviderCode();

        if (! isset($this->providers[$code])) {
            throw new \InvalidArgumentException("Payment provider [{$code}] is not registered.");
        }

        $provider = $this->providers[$code];

        if (! $provider->isAvailable()) {
            throw new \RuntimeException("Payment provider [{$code}] is not available.");
        }

        return $provider;
    }

    public function createQr(PaymentRequest $request, ?string $providerCode = null): PaymentQr
    {
        $provider = $this->provider($providerCode);

        Log::info('Creating payment QR', [
            'provider' => $provider->getCode(),
            'order' => $request->orderNumber,
            'amount' => $request->amount,
        ]);

        return $provider->createQr($request);
    }

    public function checkStatus(string $providerReference, ?string $providerCode = null): PaymentStatus
    {
        $provider = $this->provider($providerCode);

        return $provider->checkStatus($providerReference);
    }

    public function verifyWebhook(array $payload, array $headers, ?string $providerCode = null): PaymentStatus
    {
        $provider = $this->provider($providerCode);

        Log::info('Verifying webhook', [
            'provider' => $provider->getCode(),
            'payload_keys' => array_keys($payload),
        ]);

        return $provider->verifyWebhook($payload, $headers);
    }

    public function getActiveProviderCode(): string
    {
        $khqrEnabled = Setting::getValue('payments_khqr_enabled', false);
        $provider = Setting::getValue('payments_provider', '');

        if ($khqrEnabled && $provider && isset($this->providers[$provider])) {
            return $provider;
        }

        return config('payment.active_provider', 'cash');
    }

    public function getAvailableProviders(): array
    {
        return array_filter($this->providers, fn (PaymentProvider $provider) => $provider->isAvailable());
    }

    public function isKhqrAvailable(): bool
    {
        $khqrEnabled = Setting::getValue('payments_khqr_enabled', false);
        $provider = Setting::getValue('payments_provider', '');

        if (! $khqrEnabled || ! $provider) {
            return false;
        }

        return isset($this->providers[$provider]) && $this->providers[$provider]->isAvailable();
    }
}
