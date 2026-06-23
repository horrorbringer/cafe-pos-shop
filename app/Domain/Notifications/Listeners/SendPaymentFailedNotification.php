<?php

declare(strict_types=1);

namespace App\Domain\Notifications\Listeners;

use App\Domain\Notifications\Channels\EmailChannel;
use App\Domain\Notifications\Channels\TelegramChannel;
use App\Domain\Notifications\Events\PaymentFailed;
use App\Domain\Notifications\Services\NotificationSettingsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendPaymentFailedNotification implements ShouldQueue
{
    public function __construct(
        protected NotificationSettingsService $settings,
        protected EmailChannel $emailChannel,
        protected TelegramChannel $telegramChannel,
    ) {}

    public function handle(PaymentFailed $event): void
    {
        $rules = $this->settings->getEnabledRulesForEvent('payment_failed');

        if ($rules->isEmpty()) {
            Log::info('No enabled rules for payment_failed event', [
                'order_id' => $event->order->id,
            ]);

            return;
        }

        foreach ($rules as $rule) {
            $channelCode = $rule->channel_code;

            try {
                if (! $this->settings->isChannelEnabled($channelCode)) {
                    continue;
                }

                if (! $this->settings->shouldSendDueToCooldown('payment_failed', $channelCode, $rule->cooldown_minutes)) {
                    Log::info('Notification skipped due to cooldown', [
                        'event' => 'payment_failed',
                        'channel' => $channelCode,
                        'order_id' => $event->order->id,
                    ]);

                    continue;
                }

                $recipients = $this->settings->getActiveRecipients($channelCode);

                foreach ($recipients as $recipient) {
                    $this->dispatchToChannel($channelCode, $event, $recipient);

                    $this->settings->recordNotification(
                        eventCode: 'payment_failed',
                        channelCode: $channelCode,
                        recipient: $recipient->destination,
                        status: 'sent',
                        payload: [
                            'order_id' => $event->order->id,
                            'order_number' => $event->order->order_number,
                            'reason' => $event->reason,
                            'payment_method' => $event->paymentMethod,
                        ],
                    );
                }
            } catch (\Throwable $e) {
                Log::error('Failed to send payment_failed notification', [
                    'event' => 'payment_failed',
                    'channel' => $channelCode,
                    'order_id' => $event->order->id,
                    'error' => $e->getMessage(),
                ]);

                $this->settings->recordNotification(
                    eventCode: 'payment_failed',
                    channelCode: $channelCode,
                    recipient: 'unknown',
                    status: 'failed',
                    errorMessage: $e->getMessage(),
                );
            }
        }
    }

    protected function dispatchToChannel(string $channelCode, PaymentFailed $event, object $recipient): void
    {
        match ($channelCode) {
            'email' => $this->sendEmail($event, $recipient),
            'telegram' => $this->sendTelegram($event, $recipient),
            default => Log::warning('Unknown notification channel', ['channel' => $channelCode]),
        };
    }

    protected function sendEmail(PaymentFailed $event, object $recipient): void
    {
        $message = "Payment Failed\n\n"
            ."Order #{$event->order->order_number}\n"
            ."Reason: {$event->reason}\n"
            ."Method: {$event->paymentMethod}";

        $this->emailChannel->send(
            $recipient->destination,
            'Payment Failed - '.$event->order->order_number,
            $message,
        );
    }

    protected function sendTelegram(PaymentFailed $event, object $recipient): void
    {
        $message = "<b>Payment Failed</b>\n"
            ."\nOrder #{$event->order->order_number}"
            ."\nReason: {$event->reason}"
            ."\nMethod: {$event->paymentMethod}";

        $this->telegramChannel->send($recipient->destination, $message);
    }
}
