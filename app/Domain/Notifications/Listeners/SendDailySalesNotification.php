<?php

declare(strict_types=1);

namespace App\Domain\Notifications\Listeners;

use App\Domain\Notifications\Channels\EmailChannel;
use App\Domain\Notifications\Channels\TelegramChannel;
use App\Domain\Notifications\Events\DailySalesClosed;
use App\Domain\Notifications\Services\NotificationSettingsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendDailySalesNotification implements ShouldQueue
{
    public function __construct(
        protected NotificationSettingsService $settings,
        protected EmailChannel $emailChannel,
        protected TelegramChannel $telegramChannel,
    ) {}

    public function handle(DailySalesClosed $event): void
    {
        $rules = $this->settings->getEnabledRulesForEvent('daily_sales_summary');

        if ($rules->isEmpty()) {
            Log::info('No enabled rules for daily_sales_summary event', [
                'date' => $event->date,
            ]);

            return;
        }

        foreach ($rules as $rule) {
            $channelCode = $rule->channel_code;

            try {
                if (! $this->settings->isChannelEnabled($channelCode)) {
                    continue;
                }

                $recipients = $this->settings->getActiveRecipients($channelCode);

                foreach ($recipients as $recipient) {
                    $this->dispatchToChannel($channelCode, $event, $recipient);

                    $this->settings->recordNotification(
                        eventCode: 'daily_sales_summary',
                        channelCode: $channelCode,
                        recipient: $recipient->destination,
                        status: 'sent',
                        payload: [
                            'date' => $event->date,
                            'total_sales' => $event->totalSales,
                            'order_count' => $event->orderCount,
                            'top_products' => $event->topProducts,
                        ],
                    );
                }
            } catch (\Throwable $e) {
                Log::error('Failed to send daily_sales_summary notification', [
                    'event' => 'daily_sales_summary',
                    'channel' => $channelCode,
                    'date' => $event->date,
                    'error' => $e->getMessage(),
                ]);

                $this->settings->recordNotification(
                    eventCode: 'daily_sales_summary',
                    channelCode: $channelCode,
                    recipient: 'unknown',
                    status: 'failed',
                    errorMessage: $e->getMessage(),
                );
            }
        }
    }

    protected function dispatchToChannel(string $channelCode, DailySalesClosed $event, object $recipient): void
    {
        match ($channelCode) {
            'email' => $this->sendEmail($event, $recipient),
            'telegram' => $this->sendTelegram($event, $recipient),
            default => Log::warning('Unknown notification channel', ['channel' => $channelCode]),
        };
    }

    protected function sendEmail(DailySalesClosed $event, object $recipient): void
    {
        $message = "Daily Sales Summary - {$event->date}\n\n"
            .'Total Sales: $'.number_format($event->totalSales, 2)."\n"
            ."Orders: {$event->orderCount}";

        if (! empty($event->topProducts)) {
            $message .= "\n\nTop Products:\n";
            foreach ($event->topProducts as $product) {
                $message .= "- {$product['name']}: \$".number_format($product['revenue'] ?? 0, 2)."\n";
            }
        }

        $this->emailChannel->send(
            $recipient->destination,
            'Daily Sales - '.$event->date,
            $message,
        );
    }

    protected function sendTelegram(DailySalesClosed $event, object $recipient): void
    {
        $message = "<b>Daily Sales Summary</b>\n"
            ."\nDate: {$event->date}"
            ."\nTotal Sales: \$".number_format($event->totalSales, 2)
            ."\nOrders: {$event->orderCount}";

        if (! empty($event->topProducts)) {
            $message .= "\n\n<b>Top Products:</b>\n";
            foreach ($event->topProducts as $product) {
                $message .= "- {$product['name']}: \$".number_format($product['revenue'] ?? 0, 2)."\n";
            }
        }

        $this->telegramChannel->send($recipient->destination, $message);
    }
}
