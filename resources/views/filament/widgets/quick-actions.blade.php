<x-filament-widgets::widget>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="{{ route('pos') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl bg-white dark:bg-gray-800 ring-1 ring-gray-950/5 dark:ring-white/10 hover:ring-primary-500/30 transition-all hover:shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-primary-50 dark:bg-primary-500/10 flex items-center justify-center shrink-0">
                <x-filament::icon icon="heroicon-o-shopping-cart" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('New Order') }}</p>
                <p class="text-xs text-gray-400 truncate">{{ __('Open POS terminal') }}</p>
            </div>
        </a>

        <a href="{{ \App\Filament\Resources\Orders\OrderResource::getUrl('index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl bg-white dark:bg-gray-800 ring-1 ring-gray-950/5 dark:ring-white/10 hover:ring-amber-500/30 transition-all hover:shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center shrink-0">
                <x-filament::icon icon="heroicon-o-receipt-percent" class="w-5 h-5 text-amber-600 dark:text-amber-400" />
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Orders') }}</p>
                <p class="text-xs text-gray-400 truncate">{{ __('View all orders') }}</p>
            </div>
        </a>

        <a href="{{ \App\Filament\Resources\Products\ProductResource::getUrl('index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl bg-white dark:bg-gray-800 ring-1 ring-gray-950/5 dark:ring-white/10 hover:ring-emerald-500/30 transition-all hover:shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center shrink-0">
                <x-filament::icon icon="heroicon-o-cube" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Products') }}</p>
                <p class="text-xs text-gray-400 truncate">{{ __('Manage catalog') }}</p>
            </div>
        </a>

        <a href="{{ \App\Filament\Pages\ReportsPage::getUrl() }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl bg-white dark:bg-gray-800 ring-1 ring-gray-950/5 dark:ring-white/10 hover:ring-violet-500/30 transition-all hover:shadow-sm">
            <div class="w-10 h-10 rounded-lg bg-violet-50 dark:bg-violet-500/10 flex items-center justify-center shrink-0">
                <x-filament::icon icon="heroicon-o-chart-bar" class="w-5 h-5 text-violet-600 dark:text-violet-400" />
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Reports') }}</p>
                <p class="text-xs text-gray-400 truncate">{{ __('Sales analytics') }}</p>
            </div>
        </a>
    </div>
</x-filament-widgets::widget>
