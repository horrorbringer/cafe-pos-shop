<?php

namespace App\Filament\Resources\Notifications;

use App\Domain\Notifications\Models\NotificationLog;
use App\Filament\Resources\Notifications\Pages\ListNotificationLogs;
use App\Filament\Resources\Notifications\Schemas\NotificationLogForm;
use App\Filament\Resources\Notifications\Tables\NotificationLogsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NotificationLogResource extends Resource
{
    protected static ?string $model = NotificationLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?int $navigationSort = 13;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationLabel = 'Logs';

    protected static ?string $modelLabel = 'Notification Log';

    protected static ?string $pluralModelLabel = 'Notification Logs';

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
        return NotificationLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NotificationLogsTable::configure($table);
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
            'index' => ListNotificationLogs::route('/'),
        ];
    }
}
