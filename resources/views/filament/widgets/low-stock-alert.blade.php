@php
    $items = $this->getLowStockItems();
    $total = $this->getTotalLowStock();
    $inventoryUrl = $this->getInventoryUrl();
@endphp

<x-filament-widgets::widget>
    <x-filament::section
        :heading="__('Low Stock') . ($total > 5 ? ' (' . $total . ')' : '')"
        icon="heroicon-o-exclamation-triangle"
        :footer-content="false"
    >
        @if (empty($items))
            <div class="flex flex-col items-center justify-center py-8 text-gray-400">
                <x-filament::icon icon="heroicon-o-check-circle" class="w-10 h-10 mb-2" />
                <p class="text-sm">{{ __('All stocked up') }}</p>
            </div>
        @else
            <div class="space-y-2">
                @foreach ($items as $item)
                    <a href="{{ $item['edit_url'] }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-danger-50 dark:bg-danger-500/10 hover:bg-danger-100 dark:hover:bg-danger-500/20 transition-colors group">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <div class="flex items-center gap-2 min-w-0">
                                    <x-filament::icon icon="heroicon-o-cube" class="w-4 h-4 text-danger-500 shrink-0" />
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate group-hover:text-danger-700 dark:group-hover:text-danger-300">
                                        {{ $item['name'] }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 shrink-0 ml-2">
                                    <span class="text-sm font-semibold text-danger-600 dark:text-danger-400">
                                        {{ $item['quantity'] }}
                                    </span>
                                    <span class="text-xs text-gray-400">{{ $item['unit'] }}</span>
                                    <x-filament::badge color="danger" size="sm">
                                        {{ __('Low') }}
                                    </x-filament::badge>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                            <div class="flex-1 h-1.5 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500 bg-danger-400"
                                    style="width: {{ $item['ratio'] }}%">
                                </div>
                                </div>
                                @if ($item['needed'] > 0)
                                    <span class="text-xs text-danger-600 dark:text-danger-400 whitespace-nowrap">
                                        {{ __('Need') }} {{ $item['needed'] }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            @if ($total > 5)
                <div class="mt-3 text-center">
                    <a href="{{ $inventoryUrl }}"
                        class="text-xs font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300 transition-colors">
                        {{ __('View all') }} {{ $total }} {{ __('low stock items') }} &rarr;
                    </a>
                </div>
            @endif
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
