<?php

namespace App\Filament\Resources\Notifications;

use App\Domain\Notifications\Models\NotificationRule;
use App\Filament\Resources\Notifications\Pages\CreateNotificationRule;
use App\Filament\Resources\Notifications\Pages\EditNotificationRule;
use App\Filament\Resources\Notifications\Pages\ListNotificationRules;
use App\Filament\Resources\Notifications\Schemas\NotificationRuleForm;
use App\Filament\Resources\Notifications\Tables\NotificationRulesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NotificationRuleResource extends Resource
{
    protected static ?string $model = NotificationRule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 12;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $navigationLabel = 'Rules';

    protected static ?string $modelLabel = 'Notification Rule';

    protected static ?string $pluralModelLabel = 'Notification Rules';

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
        return NotificationRuleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NotificationRulesTable::configure($table);
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
            'index' => ListNotificationRules::route('/'),
            'create' => CreateNotificationRule::route('/create'),
            'edit' => EditNotificationRule::route('/{record}/edit'),
        ];
    }
}
