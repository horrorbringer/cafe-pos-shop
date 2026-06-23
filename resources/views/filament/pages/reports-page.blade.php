<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Date Filter --}}
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center bg-white dark:bg-gray-900 rounded-xl p-1 ring-1 ring-gray-950/5 dark:ring-white/10">
                @php
                    $filters = [
                        'today' => 'Today',
                        'yesterday' => 'Yesterday',
                        'last-7-days' => '7 Days',
                        'last-30-days' => '30 Days',
                        'custom' => 'Custom',
                    ];
                @endphp
                @foreach($filters as $value => $label)
                    <button
                        wire:click="$set('dateFilter', '{{ $value }}')"
                        type="button"
                        class="px-4 py-2 rounded-lg text-sm font-medium transition-all
                            {{ $dateFilter === $value
                                ? 'bg-primary-600 text-white shadow-sm'
                                : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            @if($dateFilter === 'custom')
                <div class="flex items-center gap-2">
                    <x-filament::input type="date" wire:model.live="startDate" class="w-40" />
                    <span class="text-gray-400">to</span>
                    <x-filament::input type="date" wire:model.live="endDate" class="w-40" />
                </div>
            @endif
        </div>

        @php
            $totalSales = $this->getTotalSales();
            $cashSales = $this->getCashSales();
            $khqrSales = $this->getKhqrSales();
            $refundAmount = $this->getRefundAmount();
            $orderCount = $this->getOrderCount();
            $netRevenue = $totalSales - $refundAmount;
            $topProducts = $this->getTopProducts();
        @endphp

        {{-- Summary Stats --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <x-filament::section compact>
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-success-50 dark:bg-success-500/10">
                        <x-heroicon-o-banknotes class="w-5 h-5 text-success-600 dark:text-success-400" />
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Sales</p>
                        <p class="text-xl font-bold text-success-600 dark:text-success-400">${{ number_format($totalSales, 2) }}</p>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section compact>
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-warning-50 dark:bg-warning-500/10">
                        <x-heroicon-o-currency-dollar class="w-5 h-5 text-warning-600 dark:text-warning-400" />
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Cash Sales</p>
                        <p class="text-xl font-bold text-warning-600 dark:text-warning-400">${{ number_format($cashSales, 2) }}</p>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section compact>
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-info-50 dark:bg-info-500/10">
                        <x-heroicon-o-qr-code class="w-5 h-5 text-info-600 dark:text-info-400" />
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">KHQR Sales</p>
                        <p class="text-xl font-bold text-info-600 dark:text-info-400">${{ number_format($khqrSales, 2) }}</p>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section compact>
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-primary-50 dark:bg-primary-500/10">
                        <x-heroicon-o-shopping-cart class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Orders</p>
                        <p class="text-xl font-bold text-primary-600 dark:text-primary-400">{{ number_format($orderCount) }}</p>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section compact>
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-danger-50 dark:bg-danger-500/10">
                        <x-heroicon-o-arrow-uturn-left class="w-5 h-5 text-danger-600 dark:text-danger-400" />
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Refunds</p>
                        <p class="text-xl font-bold text-danger-600 dark:text-danger-400">${{ number_format($refundAmount, 2) }}</p>
                    </div>
                </div>
            </x-filament::section>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Revenue Breakdown --}}
            <x-filament::section heading="Revenue Breakdown" icon="heroicon-o-chart-bar" icon-color="gray">

                <div class="space-y-4">
                    @if($totalSales > 0)
                        <div class="h-4 rounded-full overflow-hidden flex bg-gray-100 dark:bg-white/5">
                            @php
                                $cashPct = $totalSales > 0 ? ($cashSales / $totalSales) * 100 : 0;
                                $khqrPct = $totalSales > 0 ? ($khqrSales / $totalSales) * 100 : 0;
                            @endphp
                            @if($cashPct > 0)
                                <div class="bg-warning-400 dark:bg-warning-500 h-full transition-all" style="width: {{ $cashPct }}%"></div>
                            @endif
                            @if($khqrPct > 0)
                                <div class="bg-info-400 dark:bg-info-500 h-full transition-all" style="width: {{ $khqrPct }}%"></div>
                            @endif
                        </div>
                    @endif

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-warning-400 dark:bg-warning-500"></span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">Cash</span>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-semibold text-gray-950 dark:text-white">${{ number_format($cashSales, 2) }}</span>
                                @if($totalSales > 0)
                                    <span class="text-xs text-gray-400 ml-1">{{ number_format($cashPct, 0) }}%</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-info-400 dark:bg-info-500"></span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">KHQR</span>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-semibold text-gray-950 dark:text-white">${{ number_format($khqrSales, 2) }}</span>
                                @if($totalSales > 0)
                                    <span class="text-xs text-gray-400 ml-1">{{ number_format($khqrPct, 0) }}%</span>
                                @endif
                            </div>
                        </div>

                        @if($refundAmount > 0)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-danger-400 dark:bg-danger-500"></span>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Refunds</span>
                                </div>
                                <span class="text-sm font-semibold text-danger-600 dark:text-danger-400">-${{ number_format($refundAmount, 2) }}</span>
                            </div>
                        @endif

                        <div class="border-t border-gray-100 dark:border-white/5 pt-3 flex items-center justify-between">
                            <span class="text-sm font-bold text-gray-950 dark:text-white">Net Revenue</span>
                            <span class="text-lg font-bold text-success-600 dark:text-success-400">${{ number_format($netRevenue, 2) }}</span>
                        </div>
                    </div>
                </div>
            </x-filament::section>

            {{-- Top Products --}}
            <x-filament::section
                heading="Top Products"
                icon="heroicon-o-trophy"
                icon-color="warning"
                class="lg:col-span-2"
            >
                <x-slot name="afterHeader">
                    @if($topProducts->isNotEmpty())
                        {{ $this->exportCsvAction }}
                    @endif
                </x-slot>

                @if($topProducts->isEmpty())
                    <x-filament::empty-state
                        heading="No sales data"
                        description="No products sold in this period"
                        icon="heroicon-o-document-text"
                    />
                @else
                    @php
                        $maxRevenue = $topProducts->max('total_revenue');
                    @endphp
                    <div class="divide-y divide-gray-100 dark:divide-white/5">
                        @foreach($topProducts as $index => $product)
                            @php
                                $revenuePct = $maxRevenue > 0 ? ($product->total_revenue / $maxRevenue) * 100 : 0;
                            @endphp
                            <div class="px-1 py-3 hover:bg-gray-50 dark:hover:bg-white/5 transition rounded-lg">
                                <div class="flex items-center gap-3">
                                    <x-filament::badge color="gray" class="w-7 h-7 flex items-center justify-center shrink-0">
                                        {{ $index + 1 }}
                                    </x-filament::badge>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-1">
                                            <h4 class="text-sm font-semibold text-gray-950 dark:text-white truncate">{{ $product->product_name ?? 'Unknown' }}</h4>
                                            <span class="text-sm font-bold text-gray-950 dark:text-white ml-3">${{ number_format($product->total_revenue, 2) }}</span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <div class="flex-1 h-1.5 bg-gray-100 dark:bg-white/5 rounded-full overflow-hidden">
                                                <div class="h-full bg-primary-400 dark:bg-primary-500 rounded-full transition-all" style="width: {{ $revenuePct }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-400 whitespace-nowrap">{{ $product->total_quantity }} sold</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-filament::section>
        </div>

    </div>
</x-filament-panels::page>