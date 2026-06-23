<?php

namespace App\Filament\Resources\Notifications\Pages;

use App\Filament\Resources\Notifications\NotificationRuleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNotificationRules extends ListRecords
{
    protected static string $resource = NotificationRuleResource::class;

    protected ?string $heading = 'Notification Rules';

    protected ?string $subheading = 'Link an event to a channel. Set cooldown to avoid too many messages.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Add Rule'),
        ];
    }
}
