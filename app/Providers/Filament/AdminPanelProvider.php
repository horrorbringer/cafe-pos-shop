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
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
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
            ])
            ->navigationGroups([
                __('Dashboard'),
                __('Orders'),
                __('Menu'),
                __('Inventory'),
                __('Reports'),
                __('Settings'),
            ])
            ->navigationItems([
                NavigationItem::make(__('POS Terminal'))
                    ->url(fn (): string => url('/pos'))
                    ->icon('heroicon-o-shopping-cart')
                    ->group('Orders')
                    ->sort(0)
                    ->isActiveWhen(fn () => request()->is('pos*')),
            ])
            ->renderHook('panels::user-menu.before', fn () => Blade::render('
                @foreach(array_filter(config(\'app.supported_locales\', []), fn ($l) => $l !== app()->getLocale()) as $code)
                    <a href="{{ route(\'language.switch\', $code) }}"
                        class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition-colors hover:bg-gray-50 dark:hover:bg-gray-700">
                        <x-heroicon-o-language class="w-5 h-5 text-gray-400" />
                        <span>{{ $code === \'en\' ? __(\'English\') : __(ucfirst($code)) }}</span>
                    </a>
                @endforeach
            '))
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
