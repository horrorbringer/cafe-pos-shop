<?php

declare(strict_types=1);

namespace App\Domain\Notifications\Listeners;

use App\Domain\Notifications\Channels\EmailChannel;
use App\Domain\Notifications\Channels\TelegramChannel;
use App\Domain\Notifications\Events\OrderPaid;
use App\Domain\Notifications\Services\NotificationSettingsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendOrderPaidNotification implements ShouldQueue
{
    public function __construct(
        protected NotificationSettingsService $settings,
        protected EmailChannel $emailChannel,
        protected TelegramChannel $telegramChannel,
    ) {}

    public function handle(OrderPaid $event): void
    {
        $rules = $this->settings->getEnabledRulesForEvent('order_paid');

        if ($rules->isEmpty()) {
            Log::info('No enabled rules for order_paid event', [
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

                if (! $this->settings->shouldSendDueToCooldown('order_paid', $channelCode, $rule->cooldown_minutes)) {
                    Log::info('Notification skipped due to cooldown', [
                        'event' => 'order_paid',
                        'channel' => $channelCode,
                        'order_id' => $event->order->id,
                    ]);

                    continue;
                }

                $recipients = $this->settings->getActiveRecipients($channelCode);

                foreach ($recipients as $recipient) {
                    $this->dispatchToChannel($channelCode, $event, $recipient);

                    $this->settings->recordNotification(
                        eventCode: 'order_paid',
                        channelCode: $channelCode,
                        recipient: $recipient->destination,
                        status: 'sent',
                        payload: [
                            'order_id' => $event->order->id,
                            'order_number' => $event->order->order_number,
                            'amount_paid' => $event->amountPaid,
                            'payment_method' => $event->paymentMethod,
                        ],
                    );
                }
            } catch (\Throwable $e) {
                Log::error('Failed to send order_paid notification', [
                    'event' => 'order_paid',
                    'channel' => $channelCode,
                    'order_id' => $event->order->id,
                    'error' => $e->getMessage(),
                ]);

                $this->settings->recordNotification(
                    eventCode: 'order_paid',
                    channelCode: $channelCode,
                    recipient: 'unknown',
                    status: 'failed',
                    errorMessage: $e->getMessage(),
                );
            }
        }
    }

    protected function dispatchToChannel(string $channelCode, OrderPaid $event, object $recipient): void
    {
        match ($channelCode) {
            'email' => $this->sendEmail($event, $recipient),
            'telegram' => $this->sendTelegram($event, $recipient),
            default => Log::warning('Unknown notification channel', ['channel' => $channelCode]),
        };
    }

    protected function sendEmail(OrderPaid $event, object $recipient): void
    {
        $message = "Order Paid\n\n"
            ."Order #{$event->order->order_number}\n"
            .'Amount: $'.number_format($event->amountPaid, 2)."\n"
            ."Method: {$event->paymentMethod}";

        $this->emailChannel->send(
            $recipient->destination,
            'Order Paid - '.$event->order->order_number,
            $message,
        );
    }

    protected function sendTelegram(OrderPaid $event, object $recipient): void
    {
        $message = "<b>Order Paid</b>\n"
            ."\nOrder #{$event->order->order_number}"
            ."\nAmount: \$".number_format($event->amountPaid, 2)
            ."\nMethod: {$event->paymentMethod}";

        $this->telegramChannel->send($recipient->destination, $message);
    }
}
