<div class="flex h-screen bg-stone-50 text-stone-800 select-none"
    x-data="{ addingFlash: null, processing: $wire.entangle('processing') }"
    x-on:item-added.window="addingFlash = $event.detail.productId; setTimeout(() => addingFlash = null, 400)"
    @keydown.escape.window="if ($wire.showModifierModal) $wire.cancelModifierModal(); else if ($wire.showPaymentModal) $wire.set('showPaymentModal', false); else if ($wire.showKhqrModal) $wire.cancelKhqr(); else if ($wire.showReceiptModal) false"
    @keydown.enter.window="if ($wire.showPaymentModal && $wire.paymentMethod === 'cash' && $wire.amountTendered >= $wire.total) $wire.processPayment()">

    {{-- Left Panel: Products --}}
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">

        {{-- Top Bar --}}
        <div class="bg-white border-b border-stone-200 px-5 py-3 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <h1 class="text-lg font-bold text-stone-800 tracking-tight">POS</h1>
                @if($this->order?->order_number)
                    <span class="text-xs font-mono bg-stone-100 text-stone-500 px-2 py-0.5 rounded">{{ $this->order->order_number }}</span>
                @endif
            </div>
            <div class="flex items-center gap-2">
                @if($this->itemCount > 0)
                    <button wire:click="cancelOrder"
                        class="text-xs text-stone-400 hover:text-red-500 hover:bg-red-50 px-2 py-1 rounded transition-colors">
                        {{ __('Clear') }}
                    </button>
                @endif
            </div>
        </div>

        {{-- Order Type + Search + Categories --}}
        <div class="bg-white border-b border-stone-200 px-5 py-3 space-y-3 shrink-0">

            {{-- Order Type Toggle --}}
            <div class="flex items-center gap-1 bg-stone-100 rounded-lg p-1 w-fit">
                <button wire:click="$set('orderType', 'dine_in')"
                    class="px-4 py-1.5 rounded-md text-sm font-medium transition-all {{ $orderType === 'dine_in' ? 'bg-amber-500 text-white shadow-sm' : 'text-stone-600 hover:text-stone-800' }}">
                    {{ __('Dine-in') }}
                </button>
                <button wire:click="$set('orderType', 'takeaway')"
                    class="px-4 py-1.5 rounded-md text-sm font-medium transition-all {{ $orderType === 'takeaway' ? 'bg-amber-500 text-white shadow-sm' : 'text-stone-600 hover:text-stone-800' }}">
                    {{ __('Takeaway') }}
                </button>
                <button wire:click="$set('orderType', 'delivery')"
                    class="px-4 py-1.5 rounded-md text-sm font-medium transition-all {{ $orderType === 'delivery' ? 'bg-amber-500 text-white shadow-sm' : 'text-stone-600 hover:text-stone-800' }}">
                    {{ __('Delivery') }}
                </button>
            </div>

            <div class="flex items-center gap-3">
                {{-- Table Number --}}
                @if($orderType === 'dine_in')
                    <div class="relative">
                        <input type="text" wire:model.live="tableNumber" placeholder="{{ __('Table #') }}"
                            class="w-20 px-3 py-2 border border-stone-200 rounded-lg text-sm text-center font-medium focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-stone-50">
                    </div>
                @endif

                {{-- Search --}}
                <div class="relative flex-1 max-w-md">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search...') }}"
                        class="w-full pl-9 pr-4 py-2 border border-stone-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-stone-50"
                        x-ref="searchInput" x-init="$el.focus()">
                </div>
            </div>

            {{-- Categories --}}
            <div class="flex gap-1.5 overflow-x-auto pb-1 -mx-1 px-1 scrollbar-hide">
                @php
                    $productsList = $this->products;
                    $categoryCounts = collect($productsList)->groupBy('category_id')->map->count();
                    $totalCount = count($productsList);
                @endphp
                <button wire:click="$set('selectedCategoryId', 0)"
                    class="px-3.5 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition-all flex items-center gap-1.5 {{ $selectedCategoryId === 0 ? 'bg-stone-800 text-white' : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}">
                    {{ __('All') }}
                    @if($totalCount > 0)
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $selectedCategoryId === 0 ? 'bg-white/20' : 'bg-stone-200 text-stone-500' }}">{{ $totalCount }}</span>
                    @endif
                </button>
                @foreach($this->categories as $category)
                    @php $catCount = $categoryCounts->get($category['id'], 0); @endphp
                    @if($catCount > 0 || $selectedCategoryId === $category['id'])
                    <button wire:click="$set('selectedCategoryId', {{ $category['id'] }})"
                        class="px-3.5 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition-all flex items-center gap-1.5 {{ $selectedCategoryId === $category['id'] ? 'bg-stone-800 text-white' : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}">
                        {{ $category['name'] }}
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $selectedCategoryId === $category['id'] ? 'bg-white/20' : 'bg-stone-200 text-stone-500' }}">{{ $catCount }}</span>
                    </button>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Products Grid --}}
        <div class="flex-1 overflow-y-auto p-5 space-y-6">
            @php
                $popularProducts = collect($this->products)->filter(fn($p) => !empty($p['tags']) && in_array('popular', (array) $p['tags']));
                $regularProducts = collect($this->products)->filter(fn($p) => empty($p['tags']) || !in_array('popular', (array) $p['tags']));
            @endphp

            {{-- Favorites Section --}}
            @if($popularProducts->isNotEmpty() && $selectedCategoryId === 0 && empty($search))
                <div>
                    <div class="flex items-center gap-1.5 mb-3">
                        <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <h3 class="text-xs font-semibold text-stone-500 uppercase tracking-wider">{{ __('Popular Items') }}</h3>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2">
                        @foreach($popularProducts as $product)
                            @php
                                $isOutOfStock = $product['stock_quantity'] <= 0;
                            @endphp
                            <button
                                wire:click="addItemQuick({{ $product['id'] }})"
                                class="group relative bg-white rounded-xl border border-amber-200 p-2.5 text-left transition-all duration-150 cursor-pointer
                                    {{ $isOutOfStock ? 'opacity-40 cursor-not-allowed' : 'hover:shadow-md hover:border-amber-300 active:scale-[0.97]' }}"
                            >
                                <div class="relative">
                                    @if($product['image'])
                                        <img src="{{ asset('storage/' . $product['image']) }}" alt="{{ $product['name'] }}" class="w-full h-16 object-cover rounded-lg mb-1.5 bg-stone-100">
                                    @else
                                        <div class="w-full h-16 bg-stone-100 rounded-lg mb-1.5 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                    {{-- Quick-add overlay on hover --}}
                                    <div class="absolute inset-0 rounded-lg bg-amber-500/0 group-hover:bg-amber-500/10 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                                        <span class="bg-amber-500 text-white text-lg font-bold w-8 h-8 rounded-full flex items-center justify-center shadow-sm">+</span>
                                    </div>
                                </div>
                                <h4 class="font-semibold text-stone-800 text-xs leading-tight">{{ $product['name'] }}</h4>
                                <p class="text-amber-600 font-bold text-xs mt-0.5">${{ number_format($product['price'], 2) }}</p>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Main Grid --}}
            @if(count($this->products) === 0)
                <div class="flex flex-col items-center justify-center h-full text-stone-400">
                    <svg class="w-16 h-16 mb-3 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <p class="font-medium">{{ __('No products found') }}</p>
                    <p class="text-sm mt-1">{{ __('Try a different category or search term') }}</p>
                </div>
            @else
                <div>
                    @if($popularProducts->isNotEmpty() && $selectedCategoryId === 0 && empty($search))
                        <div class="flex items-center gap-1.5 mb-3">
                            <h3 class="text-xs font-semibold text-stone-500 uppercase tracking-wider">{{ __('All Items') }}</h3>
                        </div>
                    @endif
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                        @foreach($regularProducts as $product)
                            @php
                                $isOutOfStock = $product['stock_quantity'] <= 0;
                                $isLowStock = $product['stock_quantity'] > 0 && $product['stock_quantity'] <= 5;
                                $hasOptions = !empty($product['variants']) || !empty($product['modifier_groups']);
                            @endphp
                            <button
                                wire:click="addItemQuick({{ $product['id'] }})"
                                class="group relative bg-white rounded-xl border border-stone-200 p-3 text-left transition-all duration-150
                                    {{ $isOutOfStock ? 'opacity-40 cursor-not-allowed' : 'hover:shadow-md hover:border-stone-300 active:scale-[0.97] cursor-pointer' }}"
                            >
                                <div class="relative mb-2.5">
                                    @if($product['image'])
                                        <img src="{{ asset('storage/' . $product['image']) }}" alt="{{ $product['name'] }}"
                                            class="w-full h-28 object-cover rounded-lg bg-stone-100">
                                    @else
                                        <div class="w-full h-28 bg-stone-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-8 h-8 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                    @if(!empty($product['tags']))
                                        <div class="absolute top-1.5 left-1.5 flex flex-wrap gap-1">
                                            @foreach((array) $product['tags'] as $tag)
                                                @if($tag === 'new')
                                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-blue-500 text-white shadow-sm">{{ __('NEW') }}</span>
                                                @elseif($tag === 'signature')
                                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-purple-500 text-white shadow-sm">{{ __('SIGNATURE') }}</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                    @if($isOutOfStock)
                                        <div class="absolute inset-0 bg-black/40 rounded-lg flex items-center justify-center">
                                            <span class="text-white text-xs font-bold uppercase tracking-wider shadow-sm">{{ __('Sold Out') }}</span>
                                        </div>
                                    @endif
                                </div>
                                <h3 class="font-semibold text-stone-800 text-sm leading-tight mb-1 line-clamp-2">{{ $product['name'] }}</h3>
                                @if(!empty($product['calories']))
                                    <p class="text-[10px] text-stone-400 mb-0.5">{{ $product['calories'] }} kcal</p>
                                @endif
                                @if(!empty($product['variants']))
                                    @php
                                        $activeVariants = collect($product['variants'])->where('is_active', true);
                                        $minPrice = $product['price'] + ($activeVariants->min('price_adjustment') ?? 0);
                                        $maxPrice = $product['price'] + ($activeVariants->max('price_adjustment') ?? 0);
                                    @endphp
                                    <p class="text-amber-600 font-bold text-sm">
                                        ${{ number_format($minPrice, 2) }}@if($minPrice != $maxPrice) <span class="text-amber-400 font-normal">-{{ number_format($maxPrice, 2) }}</span>@endif
                                    </p>
                                @else
                                    <p class="text-amber-600 font-bold text-sm">${{ number_format($product['price'], 2) }}</p>
                                @endif
                                <div class="flex items-center gap-1 mt-1.5">
                                    @if($hasOptions)
                                        <span class="text-[10px] font-medium bg-blue-50 text-blue-600 px-1.5 py-0.5 rounded">{{ __('Options') }}</span>
                                    @endif
                                    @if($isLowStock && !$isOutOfStock)
                                        <span class="text-[10px] font-medium bg-orange-50 text-orange-600 px-1.5 py-0.5 rounded">{{ $product['stock_quantity'] }} {{ __('left') }}</span>
                                    @endif
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Right Panel: Cart --}}
    <div class="w-[380px] bg-white border-l border-stone-200 flex flex-col shrink-0">

        {{-- Cart Header --}}
        <div class="px-5 py-4 border-b border-stone-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h2 class="font-bold text-stone-800">{{ __('Cart') }}</h2>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-medium bg-stone-100 text-stone-500 px-2 py-0.5 rounded-full">
                        {{ $orderType === 'dine_in' ? __('Dine-in') : ($orderType === 'takeaway' ? __('Takeaway') : __('Delivery')) }}
                        @if($tableNumber) &middot; T{{ $tableNumber }} @endif
                    </span>
                    @if($this->itemCount > 0)
                        <span class="text-xs font-bold bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">
                            {{ $this->itemCount }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Cart Items --}}
        <div class="flex-1 overflow-y-auto">
            @if(empty($this->cartItems))
                <div class="flex flex-col items-center justify-center h-full text-stone-400 px-6">
                    <div class="w-20 h-20 bg-stone-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <p class="font-medium text-stone-500">{{ __('Cart is empty') }}</p>
                    <p class="text-sm text-stone-400 mt-1">{{ __('Tap a product to add it') }}</p>
                </div>
            @else
                <div class="divide-y divide-stone-100">
                    @foreach($this->cartItems as $item)
                        <div class="px-5 py-3 hover:bg-stone-50 transition-colors">
                            <div class="flex items-start gap-3">
                                {{-- Item Info --}}
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-stone-800 text-sm leading-tight">{{ $item['product_name'] ?? ($item['product']['name'] ?? __('Unknown')) }}</h4>
                                    @if(!empty($item['variant_name']))
                                        <p class="text-xs text-stone-400 mt-0.5">{{ $item['variant_name'] }}</p>
                                    @endif
                                    @if(!empty($item['modifiers']))
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach($item['modifiers'] as $modifier)
                                                <span class="text-[10px] bg-stone-100 text-stone-500 px-1.5 py-0.5 rounded">
                                                    {{ $modifier['modifier_option_name'] }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if(!empty($item['notes']))
                                        <p class="text-[10px] text-stone-400 italic mt-0.5">{{ $item['notes'] }}</p>
                                    @endif
                                    <p class="text-xs text-stone-400 mt-1">${{ number_format($item['unit_price'], 2) }} {{ __('each') }}</p>
                                </div>

                                {{-- Quantity Controls --}}
                                <div class="flex flex-col items-end gap-1.5">
                                    <div class="flex items-center gap-0 bg-stone-100 rounded-lg">
                                        <button wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] - 1 }})"
                                            class="w-8 h-8 flex items-center justify-center text-stone-500 hover:text-stone-800 hover:bg-stone-200 rounded-l-lg transition-colors text-lg font-medium">
                                            &minus;
                                        </button>
                                        <span class="w-8 h-8 flex items-center justify-center text-sm font-bold text-stone-800">{{ $item['quantity'] }}</span>
                                        <button wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] + 1 }})"
                                            class="w-8 h-8 flex items-center justify-center text-stone-500 hover:text-stone-800 hover:bg-stone-200 rounded-r-lg transition-colors text-lg font-medium">
                                            +
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <p class="font-bold text-sm text-stone-800">${{ number_format($item['total_price'], 2) }}</p>
                                        <button wire:click="removeItem({{ $item['id'] }})"
                                            class="text-stone-300 hover:text-red-500 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Cart Footer --}}
        @if($this->itemCount > 0)
            <div class="border-t border-stone-200 bg-white">
                {{-- Order Notes --}}
                <div class="px-5 pt-3">
                    <input type="text" wire:model.live="orderNotes" placeholder="{{ __('Order notes...') }}"
                        class="w-full px-3 py-2 border border-stone-200 rounded-lg text-xs focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-stone-50">
                </div>

                {{-- Discount --}}
                <div class="px-5 pt-2">
                    <div class="flex items-center gap-1.5">
                        <button wire:click="applyDiscountPercent(10)"
                            class="text-[10px] font-medium px-2 py-1 rounded border transition-colors
                                {{ $this->isActiveDiscountPercent(10) ? 'bg-green-50 border-green-300 text-green-700' : 'border-stone-200 text-stone-500 hover:border-stone-300' }}">
                            10%
                        </button>
                        <button wire:click="applyDiscountPercent(20)"
                            class="text-[10px] font-medium px-2 py-1 rounded border transition-colors
                                {{ $this->isActiveDiscountPercent(20) ? 'bg-green-50 border-green-300 text-green-700' : 'border-stone-200 text-stone-500 hover:border-stone-300' }}">
                            20%
                        </button>
                        <button wire:click="applyDiscountPercent(50)"
                            class="text-[10px] font-medium px-2 py-1 rounded border transition-colors
                                {{ $this->isActiveDiscountPercent(50) ? 'bg-green-50 border-green-300 text-green-700' : 'border-stone-200 text-stone-500 hover:border-stone-300' }}">
                            50%
                        </button>
                        @if($this->order && $this->order->discount > 0)
                            <button wire:click="applyDiscount(0)"
                                class="text-[10px] font-medium px-2 py-1 rounded border border-red-200 text-red-500 hover:bg-red-50 transition-colors">
                                {{ __('Remove') }}
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Summary --}}
                <div class="px-5 py-3 space-y-1.5">
                    <div class="flex justify-between text-sm">
                        <span class="text-stone-500">{{ __('Subtotal') }}</span>
                        <span class="font-medium">${{ number_format($this->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-stone-500">{{ __('Tax') }} ({{ config('pos.tax_rate', 0.10) * 100 }}%)</span>
                        <span class="font-medium">${{ number_format($this->tax, 2) }}</span>
                    </div>
                    @if($this->order && $this->order->discount > 0)
                        <div class="flex justify-between text-sm text-green-600">
                            <span>{{ __('Discount') }}</span>
                            <span class="font-medium">-${{ number_format($this->order->discount, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between pt-2 border-t border-stone-100">
                        <span class="font-bold text-stone-800">{{ __('Total') }}</span>
                        <span class="font-bold text-xl text-amber-600">${{ number_format($this->total, 2) }}</span>
                    </div>
                </div>

                {{-- Pay Button --}}
                <div class="px-5 pb-4">
                    <button wire:click="openPaymentModal"
                        class="w-full bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white font-bold py-3.5 rounded-xl transition-colors text-base shadow-sm shadow-amber-200">
                        {{ __('Pay') }} ${{ number_format($this->total, 2) }}
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- Modifier Modal --}}
    @if($showModifierModal && $selectedProduct)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                <div class="p-6">
                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="text-lg font-bold text-stone-800">{{ $selectedProduct['name'] }}</h3>
                            <p class="text-sm text-stone-400">{{ __('Customize your order') }}</p>
                        </div>
                        <button wire:click="cancelModifierModal" class="w-8 h-8 rounded-full bg-stone-100 hover:bg-stone-200 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Variants --}}
                    @if(!empty($selectedProduct['variants']))
                        @php $activeVariants = collect($selectedProduct['variants'])->where('is_active', true); @endphp
                        @if($activeVariants->isNotEmpty())
                            <div class="mb-5">
                                <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">{{ __('Size') }}</label>
                                <div class="grid grid-cols-3 gap-2">
                                    @foreach($activeVariants as $variant)
                                        <button
                                            wire:click="selectVariant({{ $variant['id'] }})"
                                            class="py-2.5 px-3 rounded-xl border-2 text-sm font-medium transition-all
                                                {{ $selectedVariant && $selectedVariant['id'] == $variant['id']
                                                    ? 'border-amber-500 bg-amber-50 text-amber-700'
                                                    : 'border-stone-200 hover:border-stone-300 text-stone-600' }}">
                                            {{ $variant['name'] }}
                                            @if($variant['price_adjustment'] > 0)
                                                <span class="block text-xs font-normal mt-0.5">+${{ number_format($variant['price_adjustment'], 2) }}</span>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif

                    {{-- Modifier Groups --}}
                    @if(!empty($selectedProduct['modifier_groups']))
                        @foreach($selectedProduct['modifier_groups'] as $group)
                            <div class="mb-5">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">
                                        {{ $group['name'] }}
                                        @if($group['is_required'])
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>
                                    @if($group['max_selections'] > 1)
                                        <span class="text-[10px] text-stone-400 font-medium">{{ __('Max') }} {{ $group['max_selections'] }}</span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($group['options'] as $option)
                                        @if($option['is_active'])
                                            @php
                                                $isSelected = collect($selectedModifiers)->contains('modifier_option_id', $option['id']);
                                            @endphp
                                            <button
                                                wire:click="toggleModifier({{ $option['id'] }})"
                                                class="py-2 px-3.5 rounded-xl border-2 text-sm font-medium transition-all
                                                    {{ $isSelected
                                                        ? 'border-amber-500 bg-amber-50 text-amber-700'
                                                        : 'border-stone-200 hover:border-stone-300 text-stone-600' }}">
                                                {{ $option['name'] }}
                                                @if($option['price'] > 0)
                                                    <span class="text-xs font-normal">+${{ number_format($option['price'], 2) }}</span>
                                                @endif
                                            </button>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @endif

                    {{-- Quantity --}}
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">{{ __('Quantity') }}</label>
                        <div class="inline-flex items-center bg-stone-100 rounded-xl">
                            <button wire:click="$set('itemQuantity', max(1, $this->itemQuantity - 1))"
                                class="w-12 h-12 flex items-center justify-center text-stone-600 hover:text-stone-800 rounded-l-xl hover:bg-stone-200 transition-colors text-xl font-medium">
                                &minus;
                            </button>
                            <span class="w-14 h-12 flex items-center justify-center text-lg font-bold text-stone-800">{{ $this->itemQuantity }}</span>
                            <button wire:click="$set('itemQuantity', $this->itemQuantity + 1)"
                                class="w-12 h-12 flex items-center justify-center text-stone-600 hover:text-stone-800 rounded-r-xl hover:bg-stone-200 transition-colors text-xl font-medium">
                                +
                            </button>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">{{ __('Special Instructions') }}</label>
                        <input type="text" wire:model.live="itemNotes" placeholder="{{ __('e.g. Extra hot, no sugar...') }}"
                            class="w-full px-4 py-2.5 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-stone-50">
                    </div>

                    {{-- Price Summary --}}
                    <div class="bg-stone-50 rounded-xl p-4 mb-5 space-y-1.5">
                        <div class="flex justify-between text-sm">
                            <span class="text-stone-500">{{ __('Base price') }}</span>
                            <span class="font-medium">${{ number_format($selectedProduct['price'], 2) }}</span>
                        </div>
                        @if($selectedVariant && $selectedVariant['price_adjustment'] > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-stone-500">Size ({{ $selectedVariant['name'] }})</span>
                                <span class="font-medium text-amber-600">+${{ number_format($selectedVariant['price_adjustment'], 2) }}</span>
                            </div>
                        @endif
                        @if(collect($selectedModifiers)->sum('price') > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-stone-500">{{ __('Modifiers') }}</span>
                                <span class="font-medium text-amber-600">+${{ number_format(collect($selectedModifiers)->sum('price'), 2) }}</span>
                            </div>
                        @endif
                        @php
                            $unitPrice = $selectedProduct['price']
                                + ($selectedVariant['price_adjustment'] ?? 0)
                                + collect($selectedModifiers)->sum('price');
                        @endphp
                        <div class="flex justify-between pt-2 border-t border-stone-200">
                            <span class="font-bold text-stone-800">{{ __('Unit Price') }}</span>
                            <span class="font-bold text-amber-600 text-lg">${{ number_format($unitPrice, 2) }}</span>
                        </div>
                        @if($itemQuantity > 1)
                            <div class="flex justify-between text-sm text-stone-500">
                                <span>&times;{{ $itemQuantity }}</span>
                                <span class="font-bold text-stone-800">${{ number_format($unitPrice * $itemQuantity, 2) }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3">
                        <button wire:click="cancelModifierModal"
                            class="flex-1 px-4 py-3 border border-stone-200 rounded-xl hover:bg-stone-50 text-stone-600 font-medium transition-colors">
                            {{ __('Cancel') }}
                        </button>
                        <button wire:click="confirmAddItem"
                            class="flex-1 px-4 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold transition-colors shadow-sm shadow-amber-200">
                            {{ __('Add') }} &minus; ${{ number_format($unitPrice * $itemQuantity, 2) }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Payment Modal --}}
    @if($showPaymentModal)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-stone-800 mb-5">{{ __('Process Payment') }}</h3>

                    {{-- Payment Method --}}
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">{{ __('Method') }}</label>
                        <div class="grid grid-cols-{{ $this->isKhqrAvailable ? 2 : 1 }} gap-2">
                            <button wire:click="selectPaymentMethod('cash')"
                                class="py-3 px-4 rounded-xl border-2 text-sm font-semibold transition-all flex flex-col items-center gap-1
                                    {{ $paymentMethod === 'cash' ? 'border-amber-500 bg-amber-50 text-amber-700' : 'border-stone-200 hover:border-stone-300 text-stone-600' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ __('Cash') }}
                            </button>
                            @if($this->isKhqrAvailable)
                                <button wire:click="selectPaymentMethod('khqr')"
                                    class="py-3 px-4 rounded-xl border-2 text-sm font-semibold transition-all flex flex-col items-center gap-1
                                        {{ $paymentMethod === 'khqr' ? 'border-amber-500 bg-amber-50 text-amber-700' : 'border-stone-200 hover:border-stone-300 text-stone-600' }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                    {{ __('KHQR') }}
                                </button>
                            @endif
                        </div>
                    </div>

                    @if($paymentMethod === 'cash')
                        {{-- Quick Cash Buttons --}}
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">{{ __('Quick Amount') }}</label>
                            <div class="grid grid-cols-4 gap-2">
                                @php
                                    $denominations = config('pos.cash_denominations', [1, 2, 5, 10, 20, 50, 100]);
                                    $quickAmounts = array_values(array_filter($denominations, fn($d) => $d >= $this->total));
                                    if (empty($quickAmounts)) {
                                        $quickAmounts = $denominations;
                                    }
                                @endphp
                                @foreach($quickAmounts as $amount)
                                    <button wire:click="$set('amountTendered', {{ $amount }})"
                                        class="py-2 rounded-lg bg-stone-100 hover:bg-stone-200 text-sm font-semibold text-stone-700 transition-colors
                                            {{ $amountTendered == $amount ? 'ring-2 ring-amber-500 bg-amber-50' : '' }}">
                                        ${{ $amount }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Amount Tendered --}}
                        <div class="mb-5">
                            <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">{{ __('Amount Tendered') }}</label>
                            <input type="number" wire:model.live="amountTendered" step="0.01" min="0"
                                class="w-full px-4 py-3 border border-stone-200 rounded-xl text-xl font-bold text-center focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-stone-50"
                                placeholder="0.00" x-ref="cashInput" x-init="$nextTick(() => $refs.cashInput.focus())">
                        </div>
                    @endif

                    {{-- Total & Change --}}
                    <div class="bg-stone-50 rounded-xl p-4 mb-5 space-y-2">
                        <div class="flex justify-between text-lg font-bold">
                            <span class="text-stone-800">{{ __('Total Due') }}</span>
                            <span class="text-amber-600">${{ number_format($this->total, 2) }}</span>
                        </div>
                        @if($paymentMethod === 'cash')
                            @if($amountTendered > 0)
                                @if($amountTendered >= $this->total)
                                    <div class="flex justify-between text-green-600 font-bold">
                                        <span>{{ __('Change') }}</span>
                                        <span>${{ number_format($amountTendered - $this->total, 2) }}</span>
                                    </div>
                                @else
                                    <div class="flex justify-between text-red-500 font-medium text-sm">
                                        <span>{{ __('Remaining') }}</span>
                                        <span>${{ number_format($this->total - $amountTendered, 2) }}</span>
                                    </div>
                                @endif
                            @endif
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="flex gap-3">
                        <button wire:click="$set('showPaymentModal', false)"
                            class="flex-1 px-4 py-3 border border-stone-200 rounded-xl hover:bg-stone-50 text-stone-600 font-medium transition-colors">
                            {{ __('Cancel') }}
                        </button>
                        @if($paymentMethod === 'cash')
                            <button wire:click="processPayment"
                                class="flex-1 px-4 py-3 bg-green-500 hover:bg-green-600 text-white rounded-xl font-bold transition-colors shadow-sm shadow-green-200 flex items-center justify-center gap-2
                                    {{ $amountTendered < $this->total ? 'opacity-50 cursor-not-allowed' : '' }}"
                                @if($amountTendered < $this->total) disabled @endif
                                wire:loading.attr="disabled">
                                <svg wire:loading wire:target="processPayment" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                <span wire:loading.remove wire:target="processPayment">Complete Sale</span>
                                <span wire:loading wire:target="processPayment">Processing...</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- KHQR Modal --}}
    @if($showKhqrModal && $khqrData)
        @php
            $qrSrc = $khqrData['qr_image_url'] ?? '';
            if (empty($qrSrc) && !empty($khqrData['qr_data'])) {
                $isBase64 = base64_decode($khqrData['qr_data'], true) !== false;
                if ($isBase64) {
                    $qrSrc = 'data:image/png;base64,' . $khqrData['qr_data'];
                }
            }
        @endphp
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
            x-data="{ pollInterval: null, timerInterval: null, pollCount: 0, maxPolls: 120 }"
            x-init="clearInterval(pollInterval); pollInterval = setInterval(() => { if (pollCount < maxPolls) { pollCount++; $wire.checkKhqrStatus(); } else { clearInterval(pollInterval); } }, 5000)"
            x-on:keydown.escape.window="clearInterval(pollInterval); clearInterval(timerInterval)"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-bold text-stone-800">{{ __('KHQR Payment') }}</h3>
                        <button wire:click="cancelKhqr"
                            class="w-8 h-8 rounded-full bg-stone-100 hover:bg-stone-200 flex items-center justify-center transition-colors"
                            x-on:click="clearInterval(pollInterval); clearInterval(timerInterval)">
                            <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="text-center mb-5">
                        <p class="text-sm text-stone-500 mb-3">{{ __('Scan with your banking app') }}</p>
                        <div class="bg-white border-2 border-stone-100 rounded-xl p-4 inline-block">
                            @if(!empty($qrSrc))
                                <img src="{{ $qrSrc }}" alt="KHQR Code" class="w-48 h-48">
                            @else
                                <div class="w-48 h-48 bg-stone-50 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                </div>
                            @endif
                        </div>
                        <p class="mt-3 text-2xl font-bold text-amber-600">${{ number_format($khqrData['amount'], 2) }}</p>
                    </div>

                    {{-- Timer --}}
                    <div class="bg-stone-50 rounded-xl p-4 mb-5 text-center">
                        <p class="text-xs text-stone-400 mb-1">{{ __('Expires in') }}</p>
                        <p class="text-3xl font-bold text-stone-800 font-mono"
                            x-data="{ seconds: {{ $qrExpirySeconds }} }"
                            x-init="timerInterval = setInterval(() => { if(seconds > 0) seconds--; }, 1000)"
                            x-text="Math.floor(seconds/60).toString().padStart(2,'0') + ':' + (seconds%60).toString().padStart(2,'0')">
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <button wire:click="checkKhqrStatus"
                            class="flex-1 px-4 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-bold transition-colors flex items-center justify-center gap-2"
                            wire:loading.attr="disabled">
                            <svg wire:loading wire:target="checkKhqrStatus" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <span wire:loading.remove wire:target="checkKhqrStatus">{{ __('Check Payment') }}</span>
                            <span wire:loading wire:target="checkKhqrStatus">{{ __('Checking...') }}</span>
                        </button>
                        <button wire:click="generateKhqr"
                            class="flex-1 px-4 py-3 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl font-bold transition-colors"
                            wire:loading.attr="disabled"
                            x-on:click="clearInterval(pollInterval); clearInterval(timerInterval)">
                            {{ __('Refresh QR') }}
                        </button>
                    </div>

                    <p class="text-xs text-stone-400 text-center mt-4">
                        {{ __('Auto-confirms after payment') }}
                </div>
            </div>
        </div>
    @endif

    {{-- Receipt Modal --}}
    @if($showReceiptModal)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-auto"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                <div class="p-5">
                    <div class="text-center mb-4">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-stone-800">{{ __('Payment Complete') }}</h3>
                        <p class="text-xs text-stone-400 mt-0.5">{{ __('Order has been processed successfully') }}</p>
                    </div>

                    <div class="bg-white rounded-xl p-3 font-mono text-xs leading-relaxed whitespace-pre-wrap text-stone-700 border border-stone-200 shadow-inner"
                        x-data="{ printing: false }">
                        {{ $receiptContent }}
                    </div>

                    <div class="flex gap-2.5 mt-4">
                        <button wire:click="printReceipt"
                            class="flex-1 px-3 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl font-semibold transition-colors flex items-center justify-center gap-1.5 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 18v4h12v-4M6 18v-2h12v2"/></svg>
                            {{ __('Print') }}
                        </button>
                        <button wire:click="newOrder"
                            class="flex-1 px-3 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-semibold transition-colors shadow-sm shadow-amber-200 text-sm">
                            {{ __('New Order') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Loading Overlay --}}
    <div x-show="processing" class="fixed inset-0 bg-black/20 z-[60] flex items-center justify-center"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div class="bg-white rounded-2xl shadow-2xl px-8 py-6 flex items-center gap-4">
            <svg class="animate-spin h-6 w-6 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span class="text-stone-700 font-medium">Processing...</span>
        </div>
    </div>

    {{-- Toast Notification --}}
    <div
        x-data="{ show: false, message: '', type: 'success' }"
        x-on:show-toast.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 3000)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50"
    >
        <div class="px-5 py-3 rounded-xl shadow-lg text-sm font-medium flex items-center gap-2"
            :class="type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'">
            <template x-if="type === 'success'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </template>
            <template x-if="type === 'error'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </template>
            <span x-text="message"></span>
        </div>
    </div>
</div>
