<x-filament-widgets::widget>
    <x-filament::section
        heading="Top Products Today"
        icon="heroicon-o-trophy"
        :footer-content="false"
    >
        @php $products = $this->getTopProducts(); @endphp

        @if (empty($products))
            <div class="flex flex-col items-center justify-center py-8 text-gray-400">
                <x-filament::icon icon="heroicon-o-shopping-cart" class="w-10 h-10 mb-2" />
                <p class="text-sm">No sales yet today</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($products as $product)
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold shrink-0
                            @switch($product['rank'])
                                @case(1) bg-amber-100 text-amber-700 @break
                                @case(2) bg-gray-100 text-gray-600 @break
                                @case(3) bg-orange-100 text-orange-700 @break
                                @default bg-stone-100 text-stone-500 @break
                            @endswitch
                        ">
                            {{ $product['rank'] }}
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700 truncate">
                                    {{ $product['name'] }}
                                </span>
                                <span class="text-sm font-semibold text-gray-900 ml-2">
                                    ${{ number_format($product['revenue'], 2) }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500"
                                        style="width: {{ $product['pct'] }}%"
                                        @switch($product['rank'])
                                            @case(1) @case(2) @case(3) class="bg-amber-500" @break
                                            @default class="bg-stone-300" @break
                                        @endswitch
                                    ></div>
                                </div>
                                <span class="text-xs text-gray-400 whitespace-nowrap">{{ $product['quantity'] }} sold</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
