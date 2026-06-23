<div class="bg-white rounded-lg shadow-sm">
    {{-- Header --}}
    <div class="p-6 border-b">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-gray-800">Order History</h2>
        </div>

        {{-- Filters --}}
        <div class="flex flex-wrap gap-4">
            <input
                type="text"
                wire:model.live="search"
                placeholder="Search order number..."
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
            >
            <select
                wire:model.live="statusFilter"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
            >
                <option value="">All Status</option>
                <option value="draft">Draft</option>
                <option value="pending">Pending</option>
                <option value="paid">Paid</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
                <option value="refunded">Refunded</option>
            </select>
            <input
                type="date"
                wire:model.live="dateFilter"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
            >
        </div>
    </div>

    {{-- Orders Table --}}
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cashier</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($this->orders['data'] ?? [] as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                            {{ $order['order_number'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                            {{ $order['user']['name'] ?? 'Unknown' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                            {{ collect($order['items'])->sum('quantity') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                            ${{ number_format($order['total'], 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($order['status'] === 'completed') bg-green-100 text-green-800
                                @elseif($order['status'] === 'paid') bg-blue-100 text-blue-800
                                @elseif($order['status'] === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order['status'] === 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif
                            ">
                                {{ ucfirst($order['status']) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-sm">
                            {{ \Carbon\Carbon::parse($order['created_at'])->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <button
                                wire:click="viewOrder({{ $order['id'] }})"
                                class="text-amber-600 hover:text-amber-900"
                            >
                                View
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            No orders found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if(isset($this->orders['last_page']) && $this->orders['last_page'] > 1)
        <div class="px-6 py-4 border-t">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    Showing {{ $this->orders['from'] }} to {{ $this->orders['to'] }} of {{ $this->orders['total'] }} orders
                </div>
                <div class="flex gap-1">
                    @for($i = 1; $i <= $this->orders['last_page']; $i++)
                        <button
                            wire:click="setPage({{ $i }})"
                            class="px-3 py-1 text-sm rounded {{ $this->orders['current_page'] === $i ? 'bg-amber-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
                        >
                            {{ $i }}
                        </button>
                    @endfor
                </div>
            </div>
        </div>
    @endif

    {{-- Order Detail Modal --}}
    @if($viewingOrder)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold">Order Details</h3>
                        <button wire:click="closeOrder" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-500">Order Number</p>
                            <p class="font-medium">{{ $viewingOrder['order_number'] }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Cashier</p>
                            <p class="font-medium">{{ $viewingOrder['user']['name'] ?? 'Unknown' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($viewingOrder['status'] === 'completed') bg-green-100 text-green-800
                                @elseif($viewingOrder['status'] === 'paid') bg-blue-100 text-blue-800
                                @elseif($viewingOrder['status'] === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($viewingOrder['status'] === 'cancelled') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif
                            ">
                                {{ ucfirst($viewingOrder['status']) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Date</p>
                            <p class="font-medium">{{ \Carbon\Carbon::parse($viewingOrder['created_at'])->format('M d, Y H:i') }}</p>
                        </div>
                    </div>

                    {{-- Items --}}
                    <div class="mb-6">
                        <h4 class="font-semibold mb-2">Items</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            @foreach($viewingOrder['items'] as $item)
                                <div class="flex justify-between py-2 {{ !$loop->last ? 'border-b' : '' }}">
                                    <span>{{ $item['product']['name'] ?? 'Unknown' }} x{{ $item['quantity'] }}</span>
                                    <span>${{ number_format($item['total_price'], 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Totals --}}
                    <div class="mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span>${{ number_format($viewingOrder['subtotal'], 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Tax</span>
                            <span>${{ number_format($viewingOrder['tax'], 2) }}</span>
                        </div>
                        @if($viewingOrder['discount'] > 0)
                            <div class="flex justify-between text-sm text-green-600">
                                <span>Discount</span>
                                <span>-${{ number_format($viewingOrder['discount'], 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between font-bold mt-2 pt-2 border-t">
                            <span>Total</span>
                            <span>${{ number_format($viewingOrder['total'], 2) }}</span>
                        </div>
                    </div>

                    {{-- Payments --}}
                    @if(!empty($viewingOrder['payments']))
                        <div class="mb-6">
                            <h4 class="font-semibold mb-2">Payments</h4>
                            @foreach($viewingOrder['payments'] as $payment)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">{{ ucfirst($payment['method']) }}</span>
                                    <span>${{ number_format($payment['amount'], 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Status Logs --}}
                    @if(!empty($viewingOrder['statusLogs']))
                        <div>
                            <h4 class="font-semibold mb-2">Status History</h4>
                            <div class="space-y-2">
                                @foreach($viewingOrder['statusLogs'] as $log)
                                    <div class="text-sm flex justify-between">
                                        <span class="text-gray-500">
                                            {{ ucfirst($log['from_status'] ?? 'Created') }} → {{ ucfirst($log['to_status']) }}
                                            @if($log['notes'])
                                                <span class="text-gray-400">({{ $log['notes'] }})</span>
                                            @endif
                                        </span>
                                        <span class="text-gray-400">{{ \Carbon\Carbon::parse($log['created_at'])->format('M d, H:i') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
