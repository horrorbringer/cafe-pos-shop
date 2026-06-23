<?php

namespace App\Filament\Resources\Notifications\Pages;

use App\Filament\Resources\Notifications\NotificationChannelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNotificationChannels extends ListRecords
{
    protected static string $resource = NotificationChannelResource::class;

    protected ?string $heading = 'Notification Channels';

    protected ?string $subheading = 'Email and Telegram channels for sending notifications. Add one to get started.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Add Channel'),
        ];
    }
}
