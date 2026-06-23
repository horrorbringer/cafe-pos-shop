<?php

namespace App\Filament\Resources\Notifications;

use App\Domain\Notifications\Models\NotificationRecipient;
use App\Filament\Resources\Notifications\Pages\CreateNotificationRecipient;
use App\Filament\Resources\Notifications\Pages\EditNotificationRecipient;
use App\Filament\Resources\Notifications\Pages\ListNotificationRecipients;
use App\Filament\Resources\Notifications\Schemas\NotificationRecipientForm;
use App\Filament\Resources\Notifications\Tables\NotificationRecipientsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NotificationRecipientResource extends Resource
{
    protected static ?string $model = NotificationRecipient::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?int $navigationSort = 11;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationLabel = 'Recipients';

    protected static ?string $modelLabel = 'Notification Recipient';

    protected static ?string $pluralModelLabel = 'Notification Recipients';

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return NotificationRecipientForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NotificationRecipientsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNotificationRecipients::route('/'),
            'create' => CreateNotificationRecipient::route('/create'),
            'edit' => EditNotificationRecipient::route('/{record}/edit'),
        ];
    }
}
