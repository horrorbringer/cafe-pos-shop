<?php

namespace Database\Seeders;

use App\Models\NotificationChannel;
use App\Models\NotificationRule;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $channels = [
            ['code' => 'email', 'is_enabled' => false, 'settings' => null],
            ['code' => 'telegram', 'is_enabled' => false, 'settings' => null],
        ];

        foreach ($channels as $channel) {
            NotificationChannel::firstOrCreate(
                ['code' => $channel['code']],
                $channel,
            );
        }

        $events = [
            ['event_code' => 'order_paid', 'cooldown_minutes' => 60],
            ['event_code' => 'payment_failed', 'cooldown_minutes' => 60],
            ['event_code' => 'refund_void', 'cooldown_minutes' => 60],
            ['event_code' => 'low_stock', 'cooldown_minutes' => 720],
            ['event_code' => 'daily_sales_summary', 'cooldown_minutes' => 1440],
        ];

        $channelCodes = ['email', 'telegram'];

        foreach ($events as $event) {
            foreach ($channelCodes as $channelCode) {
                NotificationRule::firstOrCreate(
                    ['event_code' => $event['event_code'], 'channel_code' => $channelCode],
                    [
                        'is_enabled' => false,
                        'cooldown_minutes' => $event['cooldown_minutes'],
                        'template_id' => null,
                    ],
                );
            }
        }
    }
}
