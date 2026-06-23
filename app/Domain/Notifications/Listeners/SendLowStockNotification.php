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
                'item_id' => $event->item->id,
            ]);

            return;
        }

        if ($event->currentStock > $event->threshold) {
            Log::info('Stock level above notification threshold', [
                'item_id' => $event->item->id,
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
                        'item_id' => $event->item->id,
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
                            'item_id' => $event->item->id,
                            'item_name' => $event->item->name,
                            'current_stock' => $event->currentStock,
                            'threshold' => $event->threshold,
                        ],
                    );
                }
            } catch (\Throwable $e) {
                Log::error('Failed to send low_stock notification', [
                    'event' => 'low_stock',
                    'channel' => $channelCode,
                    'item_id' => $event->item->id,
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
            ."Item: {$event->item->name}\n"
            ."Current Stock: {$event->currentStock}\n"
            ."Threshold: {$event->threshold}";

        $this->emailChannel->send(
            $recipient->destination,
            'Low Stock - '.$event->item->name,
            $message,
        );
    }

    protected function sendTelegram(LowStockDetected $event, object $recipient): void
    {
        $message = "<b>Low Stock Alert</b>\n"
            ."\nItem: {$event->item->name}"
            ."\nCurrent Stock: {$event->currentStock}"
            ."\nThreshold: {$event->threshold}";

        $this->telegramChannel->send($recipient->destination, $message);
    }
}
