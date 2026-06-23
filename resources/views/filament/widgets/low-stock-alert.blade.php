<x-filament-widgets::widget>
    <x-filament::section
        heading="Low Stock"
        icon="heroicon-o-exclamation-triangle"
        :footer-content="false"
    >
        @php $items = $this->getLowStockItems(); @endphp

        @if ($items->isEmpty())
            <div class="flex flex-col items-center justify-center py-8 text-gray-400">
                <x-filament::icon icon="heroicon-o-check-circle" class="w-10 h-10 mb-2" />
                <p class="text-sm">All stocked up</p>
            </div>
        @else
            <div class="space-y-2">
                @foreach ($items as $item)
                    <div class="flex items-center justify-between px-3 py-2 rounded-lg bg-danger-50 dark:bg-danger-500/10">
                        <div class="flex items-center gap-2 min-w-0">
                            <x-filament::icon icon="heroicon-o-cube" class="w-4 h-4 text-danger-500 shrink-0" />
                            <span class="text-sm text-gray-700 dark:text-gray-300 truncate">
                                {{ $item->name }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="text-sm font-semibold text-danger-600 dark:text-danger-400">
                                {{ $item->quantity }}
                            </span>
                            <span class="text-xs text-gray-400">{{ $item->unit }}</span>
                            <x-filament::badge color="danger" size="sm">
                                Low
                            </x-filament::badge>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
