<?php

declare(strict_types=1);

namespace App\Domain\Notifications\Services;

use App\Domain\Notifications\Models\NotificationChannel;
use App\Domain\Notifications\Models\NotificationLog;
use App\Domain\Notifications\Models\NotificationRecipient;
use App\Domain\Notifications\Models\NotificationRule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class NotificationSettingsService
{
    public function getEnabledRulesForEvent(string $eventCode): Collection
    {
        return NotificationRule::query()
            ->forEvent($eventCode)
            ->enabled()
            ->with('channel')
            ->get()
            ->filter(fn (NotificationRule $rule) => $rule->channel?->is_enabled);
    }

    public function isChannelEnabled(string $channelCode): bool
    {
        return NotificationChannel::query()
            ->where('code', $channelCode)
            ->where('is_enabled', true)
            ->exists();
    }

    public function getActiveRecipients(string $channelCode): Collection
    {
        return NotificationRecipient::query()
            ->where('channel_code', $channelCode)
            ->active()
            ->get();
    }

    public function shouldSendDueToCooldown(string $eventCode, string $channelCode, int $cooldownMinutes): bool
    {
        $cacheKey = "notification_cooldown:{$eventCode}:{$channelCode}";
        $lastSent = Cache::get($cacheKey);

        if ($lastSent) {
            return false;
        }

        $recentLog = NotificationLog::query()
            ->forEvent($eventCode)
            ->where('channel_code', $channelCode)
            ->where('status', 'sent')
            ->where('sent_at', '>=', now()->subMinutes($cooldownMinutes))
            ->exists();

        if ($recentLog) {
            Cache::put($cacheKey, true, $cooldownMinutes * 60);

            return false;
        }

        return true;
    }

    public function recordNotification(
        string $eventCode,
        string $channelCode,
        string $recipient,
        string $status,
        ?array $payload = null,
        ?string $errorMessage = null,
    ): NotificationLog {
        return NotificationLog::create([
            'event_code' => $eventCode,
            'channel_code' => $channelCode,
            'recipient' => $recipient,
            'status' => $status,
            'payload' => $payload,
            'error_message' => $errorMessage,
            'sent_at' => in_array($status, ['sent', 'failed']) ? now() : null,
        ]);
    }

    public function getCooldownMinutes(string $eventCode, string $channelCode): int
    {
        $rule = NotificationRule::query()
            ->forEvent($eventCode)
            ->where('channel_code', $channelCode)
            ->first();

        return $rule?->cooldown_minutes ?? 60;
    }
}
