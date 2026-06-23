<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account Information')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Name')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('John Doe'),

                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true)
                                ->placeholder('john@example.com'),
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('password')
                                ->label('Password')
                                ->type('password')
                                ->required(fn (string $operation): bool => $operation === 'create')
                                ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? bcrypt($state) : null)
                                ->dehydrated(fn (?string $state): bool => filled($state))
                                ->maxLength(255)
                                ->helperText(fn (?string $operation): string => $operation === 'edit' ? 'Leave blank to keep current password.' : ''),

                            TextInput::make('password_confirmation')
                                ->label('Confirm Password')
                                ->type('password')
                                ->required(fn (string $operation): bool => $operation === 'create')
                                ->same('password')
                                ->dehydrated(false),
                        ]),
                    ]),

                Section::make('Role & Status')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Select::make('roles')
                            ->label('Role')
                            ->relationship('roles', 'name')
                            ->name('roles')
                            ->required()
                            ->preload()
                            ->searchable()
                            ->options(fn (): array => Role::pluck('name', 'name')->toArray()),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Inactive users cannot log in.'),
                    ]),
            ]);
    }
}
