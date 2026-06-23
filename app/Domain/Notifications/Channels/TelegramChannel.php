<?php

namespace App\Domain\Notifications\Channels;

use App\Domain\Notifications\Models\NotificationChannel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramChannel
{
    private const API_BASE = 'https://api.telegram.org/bot';

    public function send(string $chatId, string $message, array $options = []): bool
    {
        try {
            $token = $this->getBotToken();

            $payload = array_merge([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ], $options);

            $response = Http::timeout(10)
                ->connectTimeout(5)
                ->retry(2, 500)
                ->post(self::API_BASE.$token.'/sendMessage', $payload);

            if ($response->successful()) {
                return true;
            }

            Log::error('Telegram API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Telegram notification failed', [
                'chat_id' => $chatId,
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function getBotToken(): string
    {
        $channel = NotificationChannel::where('code', 'telegram')
            ->where('is_enabled', true)
            ->first();

        $token = $channel?->settings['bot_token'] ?? config('notifications.telegram.bot_token');

        if (! $token) {
            throw new \RuntimeException('Telegram bot token not configured.');
        }

        return $token;
    }
}
