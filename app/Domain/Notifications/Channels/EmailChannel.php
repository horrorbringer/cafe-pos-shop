<?php

namespace App\Domain\Notifications\Channels;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailChannel
{
    public function send(string $email, string $subject, string $message, array $options = []): bool
    {
        try {
            $from = $options['from'] ?? config('mail.from.address');
            $fromName = $options['from_name'] ?? config('mail.from.name');

            Mail::raw($message, function ($mail) use ($email, $subject, $from, $fromName) {
                $mail->to($email)
                    ->subject($subject)
                    ->from($from, $fromName);
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Email notification failed', [
                'recipient' => $email,
                'subject' => $subject,
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
