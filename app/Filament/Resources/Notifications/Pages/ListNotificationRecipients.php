<?php

namespace App\Filament\Resources\Notifications\Pages;

use App\Filament\Resources\Notifications\NotificationRecipientResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNotificationRecipients extends ListRecords
{
    protected static string $resource = NotificationRecipientResource::class;

    protected ?string $heading = 'Notification Recipients';

    protected ?string $subheading = 'Add people who should receive notifications. For Email channels, use an email address. For Telegram, use a chat ID.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Add Recipient'),
        ];
    }
}
