<?php

namespace App\Filament\Resources\Notifications\Pages;

use App\Filament\Resources\Notifications\NotificationRecipientResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNotificationRecipient extends CreateRecord
{
    protected static string $resource = NotificationRecipientResource::class;
}
