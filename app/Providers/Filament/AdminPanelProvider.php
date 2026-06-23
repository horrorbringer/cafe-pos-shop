<?php

namespace App\Providers\Filament;

use App\Filament\Widgets\DailySales;
use App\Filament\Widgets\LowStockAlert;
use App\Filament\Widgets\SalesChart;
use App\Filament\Widgets\TopProducts;
use App\Http\Middleware\SetLocale;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login()
            ->brandName('POS Cafe')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                DailySales::class,
                SalesChart::class,
                TopProducts::class,
                LowStockAlert::class,
                AccountWidget::class,
            ])
            ->navigationGroups([
                'Dashboard',
                'Orders',
                'Menu',
                'Inventory',
                'Reports',
                'Settings',
            ])
            ->navigationItems([
                NavigationItem::make('POS Terminal')
                    ->url(fn (): string => url('/pos'))
                    ->icon('heroicon-o-shopping-cart')
                    ->group('Orders')
                    ->sort(0)
                    ->isActiveWhen(fn () => request()->is('pos*')),

                NavigationItem::make('English')
                    ->url(fn (): string => route('language.switch', 'en'))
                    ->icon('heroicon-o-language')
                    ->group('Settings')
                    ->sort(20)
                    ->visible(fn (): bool => app()->getLocale() !== 'en'),

                NavigationItem::make('Khmer')
                    ->url(fn (): string => route('language.switch', 'km'))
                    ->icon('heroicon-o-language')
                    ->group('Settings')
                    ->sort(21)
                    ->visible(fn (): bool => app()->getLocale() !== 'km'),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetLocale::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
