<?php

declare(strict_types=1);

namespace App\Domain\Notifications\Listeners;

use App\Domain\Notifications\Channels\EmailChannel;
use App\Domain\Notifications\Channels\TelegramChannel;
use App\Domain\Notifications\Events\LowStockDetected;
use App\Domain\Notifications\Services\NotificationSettingsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendLowStockNotification implements ShouldQueue
{
    public function __construct(
        protected NotificationSettingsService $settings,
        protected EmailChannel $emailChannel,
        protected TelegramChannel $telegramChannel,
    ) {}

    public function handle(LowStockDetected $event): void
    {
        $rules = $this->settings->getEnabledRulesForEvent('low_stock');

        if ($rules->isEmpty()) {
            Log::info('No enabled rules for low_stock event', [
                'product_id' => $event->product->id,
            ]);

            return;
        }

        if ($event->currentStock > $event->threshold) {
            Log::info('Stock level above notification threshold', [
                'product_id' => $event->product->id,
                'current_stock' => $event->currentStock,
                'threshold' => $event->threshold,
            ]);

            return;
        }

        foreach ($rules as $rule) {
            $channelCode = $rule->channel_code;

            try {
                if (! $this->settings->isChannelEnabled($channelCode)) {
                    continue;
                }

                if (! $this->settings->shouldSendDueToCooldown('low_stock', $channelCode, $rule->cooldown_minutes)) {
                    Log::info('Notification skipped due to cooldown', [
                        'event' => 'low_stock',
                        'channel' => $channelCode,
                        'product_id' => $event->product->id,
                    ]);

                    continue;
                }

                $recipients = $this->settings->getActiveRecipients($channelCode);

                foreach ($recipients as $recipient) {
                    $this->dispatchToChannel($channelCode, $event, $recipient);

                    $this->settings->recordNotification(
                        eventCode: 'low_stock',
                        channelCode: $channelCode,
                        recipient: $recipient->destination,
                        status: 'sent',
                        payload: [
                            'product_id' => $event->product->id,
                            'product_name' => $event->product->name,
                            'current_stock' => $event->currentStock,
                            'threshold' => $event->threshold,
                        ],
                    );
                }
            } catch (\Throwable $e) {
                Log::error('Failed to send low_stock notification', [
                    'event' => 'low_stock',
                    'channel' => $channelCode,
                    'product_id' => $event->product->id,
                    'error' => $e->getMessage(),
                ]);

                $this->settings->recordNotification(
                    eventCode: 'low_stock',
                    channelCode: $channelCode,
                    recipient: 'unknown',
                    status: 'failed',
                    errorMessage: $e->getMessage(),
                );
            }
        }
    }

    protected function dispatchToChannel(string $channelCode, LowStockDetected $event, object $recipient): void
    {
        match ($channelCode) {
            'email' => $this->sendEmail($event, $recipient),
            'telegram' => $this->sendTelegram($event, $recipient),
            default => Log::warning('Unknown notification channel', ['channel' => $channelCode]),
        };
    }

    protected function sendEmail(LowStockDetected $event, object $recipient): void
    {
        $message = "Low Stock Alert\n\n"
            ."Product: {$event->product->name}\n"
            ."Current Stock: {$event->currentStock}\n"
            ."Threshold: {$event->threshold}";

        $this->emailChannel->send(
            $recipient->destination,
            'Low Stock - '.$event->product->name,
            $message,
        );
    }

    protected function sendTelegram(LowStockDetected $event, object $recipient): void
    {
        $message = "<b>Low Stock Alert</b>\n"
            ."\nProduct: {$event->product->name}"
            ."\nCurrent Stock: {$event->currentStock}"
            ."\nThreshold: {$event->threshold}";

        $this->telegramChannel->send($recipient->destination, $message);
    }
}
