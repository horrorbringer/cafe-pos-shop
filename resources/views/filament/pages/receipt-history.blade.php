<x-filament::page>
    <div class="space-y-4">
        <!-- Filters -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap items-center gap-2">
                <x-filament::input.wrapper class="max-w-xs">
                    <x-filament::input
                        type="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('Search order number...') }}"
                    />
                </x-filament::input.wrapper>

                <select wire:model.live="statusFilter"
                    class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg shadow-sm">
                    <option value="">{{ __('All Status') }}</option>
                    @foreach(['paid' => 'Paid', 'completed' => 'Completed', 'refunded' => 'Refunded'] as $val => $label)
                        <option value="{{ $val }}">{{ __($label) }}</option>
                    @endforeach
                </select>

                <x-filament::input.wrapper class="w-40">
                    <x-filament::input
                        type="date"
                        wire:model.live="dateFrom"
                    />
                </x-filament::input.wrapper>
                <span class="text-gray-400 text-sm">—</span>
                <x-filament::input.wrapper class="w-40">
                    <x-filament::input
                        type="date"
                        wire:model.live="dateTo"
                    />
                </x-filament::input.wrapper>
            </div>
        </div>

        <!-- Orders table -->
        <x-filament::section>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 text-left">
                            <th class="px-3 py-2 font-medium text-gray-500 dark:text-gray-400">{{ __('Order #') }}</th>
                            <th class="px-3 py-2 font-medium text-gray-500 dark:text-gray-400">{{ __('Date') }}</th>
                            <th class="px-3 py-2 font-medium text-gray-500 dark:text-gray-400">{{ __('Items') }}</th>
                            <th class="px-3 py-2 font-medium text-gray-500 dark:text-gray-400">{{ __('Total') }}</th>
                            <th class="px-3 py-2 font-medium text-gray-500 dark:text-gray-400">{{ __('Status') }}</th>
                            <th class="px-3 py-2 font-medium text-gray-500 dark:text-gray-400">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $orders = $this->getOrders(); @endphp
                        @forelse($orders as $order)
                            <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-3 py-2 font-mono text-xs">{{ $order->order_number }}</td>
                                <td class="px-3 py-2 text-xs text-gray-500">{{ $order->created_at->format('d M Y, H:i') }}</td>
                                <td class="px-3 py-2 text-xs">{{ $order->items->count() }}</td>
                                <td class="px-3 py-2 text-xs font-medium">${{ number_format($order->total, 2) }}</td>
                                <td class="px-3 py-2 text-xs">
                                    <x-filament::badge :color="$order->status->color()">
                                        {{ $order->status->label() }}
                                    </x-filament::badge>
                                </td>
                                <td class="px-3 py-2 text-xs">
                                    <div class="flex items-center gap-1">
                                        <x-filament::button
                                            tag="a"
                                            href="{{ route('admin.receipt.show', $order) }}"
                                            target="_blank"
                                            icon="heroicon-o-printer"
                                            color="gray"
                                            size="xs"
                                        >
                                            {{ __('Print') }}
                                        </x-filament::button>
                                        <x-filament::button
                                            tag="a"
                                            href="{{ route('admin.receipt.pdf', $order) }}"
                                            target="_blank"
                                            icon="heroicon-o-arrow-down-tray"
                                            color="gray"
                                            size="xs"
                                        >
                                            PDF
                                        </x-filament::button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-8 text-center text-gray-400">
                                    {{ __('No orders found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($orders->hasPages())
                <div class="mt-4">
                    {{ $orders->links() }}
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament::page>
