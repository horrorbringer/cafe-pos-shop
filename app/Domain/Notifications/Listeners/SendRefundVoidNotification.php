<?php

namespace App\Domain\Notifications\Listeners;

use App\Domain\Notifications\Channels\EmailChannel;
use App\Domain\Notifications\Channels\TelegramChannel;
use App\Domain\Notifications\Events\RefundVoided;
use App\Domain\Notifications\Services\NotificationSettingsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendRefundVoidNotification implements ShouldQueue
{
    public function __construct(
        protected NotificationSettingsService $settings,
        protected EmailChannel $emailChannel,
        protected TelegramChannel $telegramChannel,
    ) {}

    public function handle(RefundVoided $event): void
    {
        $eventCode = 'refund_void';

        $rules = $this->settings->getEnabledRulesForEvent($eventCode);

        if ($rules->isEmpty()) {
            Log::info('No enabled rules for refund_void event', [
                'order_number' => $event->order->order_number,
            ]);

            return;
        }

        foreach ($rules as $rule) {
            $channelCode = $rule->channel_code;

            try {
                if (! $this->settings->isChannelEnabled($channelCode)) {
                    continue;
                }

                if (! $this->settings->shouldSendDueToCooldown($eventCode, $channelCode, $rule->cooldown_minutes)) {
                    Log::info('Notification skipped due to cooldown', [
                        'event' => $eventCode,
                        'channel' => $channelCode,
                        'order_number' => $event->order->order_number,
                    ]);

                    continue;
                }

                $recipients = $this->settings->getActiveRecipients($channelCode);

                foreach ($recipients as $recipient) {
                    $this->dispatchToChannel($channelCode, $event, $recipient);

                    $this->settings->recordNotification(
                        eventCode: $eventCode,
                        channelCode: $channelCode,
                        recipient: $recipient->destination,
                        status: 'sent',
                        payload: [
                            'order_number' => $event->order->order_number,
                            'type' => $event->type,
                            'reason' => $event->reason,
                            'amount' => $event->order->total,
                            'processed_by' => $event->user->name,
                        ],
                    );
                }
            } catch (\Throwable $e) {
                Log::error('Failed to send refund_void notification', [
                    'event' => $eventCode,
                    'channel' => $channelCode,
                    'order_number' => $event->order->order_number,
                    'error' => $e->getMessage(),
                ]);

                $this->settings->recordNotification(
                    eventCode: $eventCode,
                    channelCode: $channelCode,
                    recipient: 'unknown',
                    status: 'failed',
                    errorMessage: $e->getMessage(),
                );
            }
        }
    }

    protected function dispatchToChannel(string $channelCode, RefundVoided $event, object $recipient): void
    {
        match ($channelCode) {
            'email' => $this->sendEmail($event, $recipient),
            'telegram' => $this->sendTelegram($event, $recipient),
            default => Log::warning('Unknown notification channel', ['channel' => $channelCode]),
        };
    }

    protected function sendEmail(RefundVoided $event, object $recipient): void
    {
        $message = "Refund/Void Notice\n\n"
            ."Order: {$event->order->order_number}\n"
            ."Type: {$event->type}\n"
            ."Reason: {$event->reason}\n"
            .'Amount: $'.number_format($event->order->total, 2)."\n"
            ."Processed by: {$event->user->name}";

        $this->emailChannel->send(
            $recipient->destination,
            'Refund/Void - '.$event->order->order_number,
            $message,
        );
    }

    protected function sendTelegram(RefundVoided $event, object $recipient): void
    {
        $message = "<b>Refund/Void Notice</b>\n"
            ."\nOrder: {$event->order->order_number}"
            ."\nType: {$event->type}"
            ."\nReason: {$event->reason}"
            ."\nAmount: \$".number_format($event->order->total, 2)
            ."\nProcessed by: {$event->user->name}";

        $this->telegramChannel->send($recipient->destination, $message);
    }
}
