<div class="flex h-screen bg-stone-50 text-stone-800 select-none"
    x-data="{
        addingFlash: null,
        processing: $wire.entangle('processing'),
        browserPrint() {
            setTimeout(() => {
                const el = document.getElementById('receipt-print-area');
                if (el) {
                    const w = window.open('', '_blank', 'width=400,height=600');
                    w.document.write('<pre style=\'font-family:monospace;font-size:12px;padding:20px\'>' + el.innerText + '<\/pre>');
                    w.document.close();
                    w.print();
                    w.close();
                }
            }, 300);
        }
    }"
    x-on:item-added.window="addingFlash = $event.detail.productId; setTimeout(() => addingFlash = null, 400)"
    x-on:browser-print.window="browserPrint()"
    @keydown.escape.window="if ($wire.showModifierModal) $wire.cancelModifierModal(); else if ($wire.showPaymentModal) $wire.set('showPaymentModal', false); else if ($wire.showKhqrModal) $wire.cancelKhqr(); else if ($wire.showReceiptModal) false"
    @keydown.enter.window="if ($wire.showPaymentModal && $wire.paymentMethod === 'cash' && $wire.amountTendered >= $wire.total) $wire.processPayment()"
    @keydown.f2.window.prevent="$wire.openPaymentModal()"
    @keydown.f4.window.prevent="$wire.holdOrder()"
    @keydown.f9.window.prevent="$wire.cancelOrder()">

    {{-- Left Panel: Products --}}
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">

        {{-- Top Bar --}}
        <div class="bg-white border-b border-stone-200 px-5 py-3 flex items-center gap-4 shrink-0">
            <div class="flex items-center gap-2.5 shrink-0">
                <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center shadow-sm">
                    <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
                    </svg>
                </div>
                <h1 class="text-lg font-bold text-stone-800 tracking-tight">POS</h1>
            </div>

            {{-- Search --}}
            <div class="relative flex-1 max-w-md">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ __('Search products...') }}"
                    class="w-full pl-10 pr-4 py-2 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500/40 focus:border-amber-400 bg-stone-50 placeholder:text-stone-400"
                    x-ref="searchInput" x-init="$el.focus()">
                @if($search)
                    <button wire:click="$set('search', '')" class="absolute right-3 top-1/2 -translate-y-1/2 text-stone-400 hover:text-stone-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                @endif
            </div>

            <div class="flex items-center gap-1.5 ml-auto">
                <button onclick="window.open('{{ route('pos.customer-display') }}', 'customer-display', 'width=800,height=600')"
                    class="text-xs text-stone-500 hover:text-indigo-600 hover:bg-indigo-50 px-2.5 py-1.5 rounded-lg transition-colors flex items-center gap-1.5"
                    title="{{ __('Open customer display') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25A2.25 2.25 0 015.25 3h13.5A2.25 2.25 0 0121 5.25z"/>
                    </svg>
                    {{ __('Display') }}
                </button>
                @if($this->itemCount > 0)
                    <button wire:click="holdOrder"
                        class="text-xs text-stone-500 hover:text-amber-600 hover:bg-amber-50 px-2.5 py-1.5 rounded-lg transition-colors flex items-center gap-1.5 font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M14.25 9v6m-4.5 0V9M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ __('Hold') }}
                        <kbd class="text-[9px] font-mono bg-stone-100 text-stone-500 px-1.5 py-0.5 rounded">F4</kbd>
                    </button>
                @endif
                @php $heldCount = count($this->suspendedOrders); @endphp
                @if($heldCount > 0)
                    <button wire:click="$toggle('showSuspendedOrders')"
                        class="relative text-xs text-stone-500 hover:text-blue-600 hover:bg-blue-50 px-2.5 py-1.5 rounded-lg transition-colors flex items-center gap-1.5 font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                        </svg>
                        {{ __('Held') }}
                        <span class="bg-blue-500 text-white text-[9px] font-bold w-4.5 h-4.5 rounded-full flex items-center justify-center">{{ $heldCount }}</span>
                    </button>
                @endif
                @if($this->itemCount > 0)
                    <button wire:click="cancelOrder"
                        class="text-xs text-stone-400 hover:text-red-500 hover:bg-red-50 px-2.5 py-1.5 rounded-lg transition-colors flex items-center gap-1.5 font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                        </svg>
                        {{ __('Clear') }}
                    </button>
                @endif
                <x-language-switcher />
            </div>
        </div>

        {{-- Categories + Order Type + Search --}}
        <div class="bg-white border-b border-stone-200 px-5 py-3 space-y-3 shrink-0">

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
                    <button wire:key="cat-{{ $category['id'] }}" wire:click="$set('selectedCategoryId', {{ $category['id'] }})"
                        class="px-3.5 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition-all flex items-center gap-1.5 {{ $selectedCategoryId === $category['id'] ? 'bg-stone-800 text-white' : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}">
                        {{ $category['name'] }}
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $selectedCategoryId === $category['id'] ? 'bg-white/20' : 'bg-stone-200 text-stone-500' }}">{{ $catCount }}</span>
                    </button>
                    @endif
                @endforeach
            </div>

            {{-- Order Type Toggle --}}
            <div class="flex items-center gap-1 bg-stone-100 rounded-xl p-1 w-fit">
                <button wire:click="$set('orderType', 'dine_in')"
                    class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-all flex items-center gap-1.5 {{ $orderType === 'dine_in' ? 'bg-amber-500 text-white shadow-sm' : 'text-stone-600 hover:text-stone-800 hover:bg-stone-200' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.871c1.355 0 2.697.056 4.024.166C17.155 8.51 18 9.473 18 10.608v2.513M15 8.25v-1.5m-6 1.5v-1.5m12 9.75l-1.5.75a3.354 3.354 0 01-3 0 3.354 3.354 0 00-3 0 3.354 3.354 0 01-3 0 3.354 3.354 0 00-3 0 3.354 3.354 0 01-3 0L3 16.5m15-12.75H6"/></svg>
                    {{ __('Dine-in') }}
                </button>
                <button wire:click="$set('orderType', 'takeaway')"
                    class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-all flex items-center gap-1.5 {{ $orderType === 'takeaway' ? 'bg-amber-500 text-white shadow-sm' : 'text-stone-600 hover:text-stone-800 hover:bg-stone-200' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                    {{ __('Takeaway') }}
                </button>
                <button wire:click="$set('orderType', 'delivery')"
                    class="px-3.5 py-1.5 rounded-lg text-sm font-medium transition-all flex items-center gap-1.5 {{ $orderType === 'delivery' ? 'bg-amber-500 text-white shadow-sm' : 'text-stone-600 hover:text-stone-800 hover:bg-stone-200' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>
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
                            <button wire:key="popular-{{ $product['id'] }}"
                                wire:click="addItemQuick({{ $product['id'] }})"
                                class="group relative bg-white rounded-xl border-2 border-amber-200 p-3 text-left transition-all duration-150 cursor-pointer flex flex-col
                                    {{ $isOutOfStock ? 'opacity-40 cursor-not-allowed' : 'hover:shadow-lg hover:-translate-y-0.5 active:scale-[0.97]' }}"
                            >
                                <div class="relative mb-2">
                                    @if($product['image'])
                                        <img src="{{ asset('storage/' . $product['image']) }}" alt="{{ $product['name'] }}" class="w-full h-20 object-cover rounded-xl bg-stone-100">
                                    @else
                                        <div class="w-full h-20 bg-gradient-to-br from-amber-50 to-stone-100 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1.001A3.75 3.75 0 0012 18z"/></svg>
                                        </div>
                                    @endif
                                    <div class="absolute -top-1 -right-1 w-6 h-6 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    </div>
                                </div>
                                <h4 class="font-semibold text-stone-800 text-sm leading-tight">{{ $product['name'] }}</h4>
                                <p class="text-amber-600 font-bold text-sm mt-1">${{ number_format($product['price'], 2) }}</p>
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
                            <button wire:key="product-{{ $product['id'] }}"
                                wire:click="addItemQuick({{ $product['id'] }})"
                                wire:loading.class="opacity-70 pointer-events-none"
                                wire:target="addItemQuick({{ $product['id'] }})"
                                class="group relative bg-white rounded-xl border-2 p-4 text-left transition-all duration-150 flex flex-col
                                    {{ $isOutOfStock
                                        ? 'opacity-40 cursor-not-allowed border-stone-100'
                                        : 'hover:shadow-lg hover:-translate-y-0.5 active:scale-[0.97] cursor-pointer ' . ($hasOptions ? 'border-stone-200 hover:border-amber-300' : 'border-stone-200 hover:border-green-300') }}"
                            >
                                <div class="relative mb-3">
                                    @if($product['image'])
                                        <img src="{{ asset('storage/' . $product['image']) }}" alt="{{ $product['name'] }}"
                                            class="w-full h-32 object-cover rounded-xl bg-stone-100">
                                    @else
                                        <div class="w-full h-32 bg-gradient-to-br from-stone-100 to-stone-50 rounded-xl flex items-center justify-center">
                                            <svg class="w-10 h-10 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.974 5.974 0 01-2.133-1.001A3.75 3.75 0 0012 18z"/></svg>
                                        </div>
                                    @endif
                                    @if(!empty($product['tags']))
                                        <div class="absolute top-2 left-2 flex flex-wrap gap-1">
                                            @foreach((array) $product['tags'] as $tag)
                                                @if($tag === 'new')
                                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-blue-500 text-white shadow-sm flex items-center gap-0.5">
                                                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd"/></svg>
                                                        {{ __('NEW') }}
                                                    </span>
                                                @elseif($tag === 'signature')
                                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-purple-500 text-white shadow-sm flex items-center gap-0.5">
                                                        <svg class="w-2.5 h-2.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                                                        {{ __('SIGNATURE') }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                    @if($isOutOfStock)
                                        <div class="absolute inset-0 bg-black/40 rounded-xl flex items-center justify-center">
                                            <span class="text-white text-sm font-bold uppercase tracking-wider shadow-sm">{{ __('Sold Out') }}</span>
                                        </div>
                                    @endif
                                    @if(!$hasOptions && !$isOutOfStock)
                                        <div class="absolute top-2 right-2 w-7 h-7 bg-green-500 text-white rounded-full flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-all scale-75 group-hover:scale-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 flex flex-col justify-between gap-1">
                                    <h3 class="font-semibold text-stone-800 text-sm leading-snug line-clamp-2">{{ $product['name'] }}</h3>
                                    @if(!empty($product['calories']))
                                        <p class="text-[10px] text-stone-400">{{ $product['calories'] }} kcal</p>
                                    @endif
                                    @if(!empty($product['variants']))
                                        @php
                                            $activeVariants = collect($product['variants'])->where('is_active', true);
                                            $minPrice = $product['price'] + ($activeVariants->min('price_adjustment') ?? 0);
                                            $maxPrice = $product['price'] + ($activeVariants->max('price_adjustment') ?? 0);
                                        @endphp
                                        <p class="text-amber-600 font-bold text-sm mt-auto">
                                            ${{ number_format($minPrice, 2) }}@if($minPrice != $maxPrice) <span class="text-amber-400 font-normal">-{{ number_format($maxPrice, 2) }}</span>@endif
                                        </p>
                                    @else
                                        <p class="text-amber-600 font-bold text-base mt-auto">${{ number_format($product['price'], 2) }}</p>
                                    @endif
                                    <div class="flex items-center gap-1 flex-wrap">
                                        @if($hasOptions)
                                            <span class="text-[10px] font-medium bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full flex items-center gap-0.5">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/></svg>
                                                {{ __('Options') }}
                                            </span>
                                        @elseif(!$isOutOfStock)
                                            <span class="text-[10px] font-medium bg-green-50 text-green-600 px-2 py-0.5 rounded-full flex items-center gap-0.5">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                {{ __('Quick add') }}
                                            </span>
                                        @endif
                                        @if($isLowStock && !$isOutOfStock)
                                            <span class="text-[10px] font-medium bg-orange-50 text-orange-600 px-2 py-0.5 rounded-full flex items-center gap-0.5">
                                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/></svg>
                                                {{ $product['stock_quantity'] }} {{ __('left') }}
                                            </span>
                                        @endif
                                    </div>
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
                    <div>
                        <h2 class="font-bold text-stone-800">{{ __('Cart') }}</h2>
                        @if($this->order)
                            <p class="text-[10px] text-stone-400 font-mono">{{ $this->order->order_number }}</p>
                        @endif
                    </div>
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
                    <div class="w-20 h-20 bg-gradient-to-br from-stone-100 to-stone-50 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-9 h-9 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-stone-500">{{ __('Cart is empty') }}</p>
                    <p class="text-xs text-stone-400 mt-1 text-center">{{ __('Tap a product to add it here') }}</p>
                </div>
            @else
                @foreach($this->cartItems as $item)
                    <div wire:key="cart-{{ $item['id'] }}" class="px-5 py-3 hover:bg-stone-50 transition-colors border-b border-stone-100 last:border-b-0">
                        <div class="flex items-start gap-3">
                            {{-- Item Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <h4 class="font-semibold text-stone-800 text-sm leading-tight">{{ $item['product_name'] ?? ($item['product']['name'] ?? __('Unknown')) }}</h4>
                                    <p class="font-bold text-sm text-stone-800 shrink-0">${{ number_format($item['total_price'], 2) }}</p>
                                </div>
                                @if(!empty($item['variant_name']))
                                    <p class="text-xs text-stone-400 mt-0.5">{{ $item['variant_name'] }}</p>
                                @endif
                                @if(!empty($item['modifiers']))
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @foreach($item['modifiers'] as $modifier)
                                            <span class="text-[10px] bg-amber-50 text-amber-600 px-1.5 py-0.5 rounded">
                                                {{ $modifier['modifier_option_name'] }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                                @if(!empty($item['notes']))
                                    <p class="text-[10px] text-stone-400 italic mt-0.5">{{ $item['notes'] }}</p>
                                @endif
                                <div class="flex items-center gap-2 mt-2">
                                    <div class="inline-flex items-center bg-stone-100 rounded-lg overflow-hidden">
                                        <button wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] - 1 }})"
                                            class="w-7 h-7 flex items-center justify-center text-stone-500 hover:text-white hover:bg-red-400 transition-colors text-sm font-bold">
                                            @if($item['quantity'] === 1)
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                            @else
                                                &minus;
                                            @endif
                                        </button>
                                        <span class="min-w-[1.75rem] h-7 flex items-center justify-center text-xs font-bold text-stone-800 bg-white border-x border-stone-200">{{ $item['quantity'] }}</span>
                                        <button wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] + 1 }})"
                                            class="w-7 h-7 flex items-center justify-center text-stone-500 hover:text-white hover:bg-green-500 transition-colors text-sm font-bold">
                                            +
                                        </button>
                                    </div>
                                    <span class="text-[10px] text-stone-400">${{ number_format($item['unit_price'], 2) }} {{ __('each') }}</span>
                                    <button wire:click="removeItem({{ $item['id'] }})"
                                        class="ml-auto w-6 h-6 rounded-md flex items-center justify-center text-stone-300 hover:text-red-500 hover:bg-red-50 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Cart Footer --}}
        @if($this->itemCount > 0)
            <div class="border-t border-stone-200 bg-white">
                {{-- Order Notes --}}
                <div class="px-5 pt-3">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                        </svg>
                        <input type="text" wire:model.live="orderNotes" placeholder="{{ __('Order notes...') }}"
                            class="w-full pl-9 pr-4 py-2 border border-stone-200 rounded-lg text-xs focus:ring-2 focus:ring-amber-500/40 focus:border-amber-400 bg-stone-50 placeholder:text-stone-400">
                    </div>
                </div>

                {{-- Discount --}}
                <div class="px-5 pt-2.5">
                    <div class="flex items-center gap-1.5 mb-1.5">
                        <svg class="w-3 h-3 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6h.008v.008H6V6z"/>
                        </svg>
                        <span class="text-[10px] font-semibold text-stone-400 uppercase tracking-wider">{{ __('Discount') }}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button wire:click="applyDiscountPercent(10)"
                            class="text-[10px] font-medium px-2.5 py-1 rounded-md border transition-all
                                {{ $this->isActiveDiscountPercent(10) ? 'bg-green-50 border-green-300 text-green-700 shadow-sm' : 'border-stone-200 text-stone-500 hover:border-stone-300 hover:bg-stone-50' }}">
                            10%
                        </button>
                        <button wire:click="applyDiscountPercent(20)"
                            class="text-[10px] font-medium px-2.5 py-1 rounded-md border transition-all
                                {{ $this->isActiveDiscountPercent(20) ? 'bg-green-50 border-green-300 text-green-700 shadow-sm' : 'border-stone-200 text-stone-500 hover:border-stone-300 hover:bg-stone-50' }}">
                            20%
                        </button>
                        <button wire:click="applyDiscountPercent(50)"
                            class="text-[10px] font-medium px-2.5 py-1 rounded-md border transition-all
                                {{ $this->isActiveDiscountPercent(50) ? 'bg-green-50 border-green-300 text-green-700 shadow-sm' : 'border-stone-200 text-stone-500 hover:border-stone-300 hover:bg-stone-50' }}">
                            50%
                        </button>
                        @if($this->order && $this->order->discount > 0)
                            <button wire:click="applyDiscount(0)"
                                class="text-[10px] font-medium px-2.5 py-1 rounded-md border border-red-200 text-red-500 hover:bg-red-50 transition-all flex items-center gap-0.5">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
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
                            <span class="flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/></svg>
                                {{ __('Discount') }}
                            </span>
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
                        class="w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 active:from-amber-700 active:to-amber-800 text-white font-bold py-3.5 rounded-xl transition-all text-base shadow-md shadow-amber-200/50 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
                        {{ __('Pay') }} ${{ number_format($this->total, 2) }}
                        <kbd class="text-[10px] font-mono bg-white/20 px-1.5 py-0.5 rounded">F2</kbd>
                    </button>
                </div>
            </div>
        @endif
    </div>

    {{-- Suspended Orders Panel --}}
    @if($showSuspendedOrders && count($this->suspendedOrders) > 0)
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            wire:key="suspended-orders-panel">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[80vh] overflow-y-auto"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                <div class="p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-stone-800">{{ __('Held Orders') }}</h3>
                        <button wire:click="$set('showSuspendedOrders', false)"
                            class="w-7 h-7 rounded-full bg-stone-100 hover:bg-stone-200 flex items-center justify-center transition-colors">
                            <svg class="w-3.5 h-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="space-y-2">
                        @foreach($this->suspendedOrders as $held)
                            <button wire:click="resumeOrder({{ $held['id'] }})"
                                wire:key="held-{{ $held['id'] }}"
                                class="w-full text-left p-3.5 rounded-xl border border-stone-200 hover:border-blue-300 hover:bg-blue-50 transition-all flex items-center justify-between group">
                                <div>
                                    <p class="font-semibold text-stone-800 text-sm">{{ $held['order_number'] ?? '#' . $held['id'] }}</p>
                                    <p class="text-xs text-stone-400 mt-0.5">
                                        {{ $held['items_count'] ?? 0 }} {{ __('items') }}
                                        &middot; {{ \Carbon\Carbon::parse($held['updated_at'])->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="text-blue-500 group-hover:translate-x-0.5 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </button>
                        @endforeach
                    </div>
                    @if(count($this->suspendedOrders) === 0)
                        <p class="text-center text-stone-400 py-6 text-sm">{{ __('No held orders') }}</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

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
                                        <button wire:key="variant-{{ $variant['id'] }}"
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
                                            <button wire:key="mod-{{ $option['id'] }}"
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
                        <div class="flex items-center gap-3">
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
                            <div class="flex gap-1.5">
                                @foreach([1, 2, 3, 4, 5] as $qty)
                                    <button wire:key="qty-{{ $qty }}" wire:click="$set('itemQuantity', {{ $qty }})"
                                        class="w-10 h-12 rounded-xl text-sm font-bold transition-all
                                            {{ $this->itemQuantity === $qty
                                                ? 'bg-amber-500 text-white shadow-sm'
                                                : 'bg-stone-100 text-stone-600 hover:bg-stone-200' }}">
                                        {{ $qty }}
                                    </button>
                                @endforeach
                            </div>
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
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 bg-amber-100 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-stone-800">{{ __('Payment') }}</h3>
                        </div>
                        <span class="text-2xl font-bold text-amber-600">${{ number_format($this->total, 2) }}</span>
                    </div>

                    {{-- Payment Method --}}
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">{{ __('Method') }}</label>
                        <div class="grid grid-cols-{{ $this->isKhqrAvailable ? 2 : 1 }} gap-3">
                            <button wire:click="selectPaymentMethod('cash')"
                                class="py-3.5 px-4 rounded-xl border-2 text-sm font-semibold transition-all flex flex-col items-center justify-center gap-1.5
                                    {{ $paymentMethod === 'cash' ? 'border-green-500 bg-green-50 text-green-700 ring-1 ring-green-200' : 'border-stone-200 hover:border-stone-300 text-stone-600 hover:bg-stone-50' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
                                {{ __('Cash') }}
                            </button>
                            @if($this->isKhqrAvailable)
                                <button wire:click="selectPaymentMethod('khqr')"
                                    class="py-3.5 px-4 rounded-xl border-2 text-sm font-semibold transition-all flex flex-col items-center justify-center gap-1.5
                                        {{ $paymentMethod === 'khqr' ? 'border-blue-500 bg-blue-50 text-blue-700 ring-1 ring-blue-200' : 'border-stone-200 hover:border-stone-300 text-stone-600 hover:bg-stone-50' }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z"/></svg>
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
                                @php $denominations = config('pos.cash_denominations', [1, 2, 5, 10, 20, 50, 100]); @endphp
                                @foreach($denominations as $amount)
                                    <button wire:key="cash-{{ $amount }}" wire:click="$set('amountTendered', {{ $amount }})"
                                        class="py-2.5 rounded-lg text-sm font-semibold transition-all
                                            {{ $amountTendered == $amount
                                                ? 'ring-2 ring-amber-500 bg-amber-50 text-amber-700'
                                                : ($amount >= $this->total ? 'bg-stone-100 hover:bg-stone-200 text-stone-700' : 'bg-stone-50 text-stone-400') }}">
                                        ${{ $amount }}
                                    </button>
                                @endforeach
                                <button wire:click="$set('amountTendered', {{ $this->total }})"
                                    class="py-2.5 rounded-lg text-sm font-bold transition-all
                                        {{ $amountTendered == $this->total
                                            ? 'ring-2 ring-green-500 bg-green-50 text-green-700'
                                            : 'bg-green-100 hover:bg-green-200 text-green-700' }}">
                                    {{ __('Exact') }}
                                </button>
                            </div>
                        </div>

                        {{-- Amount Tendered --}}
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">{{ __('Amount Tendered') }}</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-2xl font-bold text-stone-400">$</span>
                                <input type="number" wire:model.live="amountTendered" step="0.01" min="0"
                                    class="w-full pl-10 pr-4 py-4 border-2 rounded-xl text-2xl font-bold text-center focus:ring-2 focus:ring-green-500/40 focus:border-green-400 bg-stone-50
                                        {{ $amountTendered >= $this->total ? 'border-green-300 bg-green-50/50' : 'border-stone-200' }}"
                                    placeholder="0.00" x-ref="cashInput" x-init="$nextTick(() => $refs.cashInput.focus()); $refs.cashInput.select()">
                                @if($amountTendered >= $this->total && $amountTendered > 0)
                                    <div class="absolute right-4 top-1/2 -translate-y-1/2">
                                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Total & Change --}}
                    <div class="bg-stone-50 rounded-xl p-4 mb-5 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-stone-500 text-sm">{{ __('Subtotal') }}</span>
                            <span class="font-medium text-sm">${{ number_format($this->subtotal, 2) }}</span>
                        </div>
                        @if($this->order && $this->order->discount > 0)
                            <div class="flex justify-between text-green-600 text-sm">
                                <span>{{ __('Discount') }}</span>
                                <span class="font-medium">-${{ number_format($this->order->discount, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between border-t border-stone-200 pt-2">
                            <span class="font-bold text-stone-800">{{ __('Total') }}</span>
                            <span class="font-bold text-xl text-amber-600">${{ number_format($this->total, 2) }}</span>
                        </div>
                        @if($paymentMethod === 'cash' && $amountTendered > 0)
                            <div class="flex justify-between pt-1 border-t border-dashed border-stone-300">
                                <span class="font-semibold text-sm">{{ $amountTendered >= $this->total ? __('Change') : __('Remaining') }}</span>
                                <span class="font-bold text-lg {{ $amountTendered >= $this->total ? 'text-green-600' : 'text-red-500' }}">
                                    @if($amountTendered >= $this->total)
                                        ${{ number_format($amountTendered - $this->total, 2) }}
                                    @else
                                        -${{ number_format($this->total - $amountTendered, 2) }}
                                    @endif
                                </span>
                            </div>
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
                                class="flex-[2] px-4 py-3 bg-green-500 hover:bg-green-600 active:bg-green-700 text-white rounded-xl font-bold transition-all shadow-sm shadow-green-200 flex items-center justify-center gap-2 text-base
                                    {{ $amountTendered < $this->total ? 'opacity-50 cursor-not-allowed' : '' }}"
                                @if($amountTendered < $this->total) disabled @endif
                                wire:loading.attr="disabled">
                                <svg wire:loading wire:target="processPayment" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                <span wire:loading.remove wire:target="processPayment">
                                    {{ $amountTendered >= $this->total ? __('Complete Sale') : __('Pay') }} ${{ number_format(min($this->total, $amountTendered), 2) }}
                                    @if($amountTendered >= $this->total)
                                        <kbd class="text-[10px] font-mono bg-white/20 px-1.5 py-0.5 rounded ml-1">Enter</kbd>
                                    @endif
                                </span>
                                <span wire:loading wire:target="processPayment">{{ __('Processing...') }}</span>
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
                    <div class="text-center mb-4"
                        x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
                        <div class="relative w-16 h-16 mx-auto mb-3">
                            <div class="absolute inset-0 bg-green-100 rounded-full"
                                x-show="show"
                                x-transition:enter="transition ease-out duration-500"
                                x-transition:enter-start="scale-0 opacity-0"
                                x-transition:enter-end="scale-100 opacity-100"></div>
                            <div class="absolute inset-0 flex items-center justify-center"
                                x-show="show"
                                x-transition:enter="transition ease-out duration-300 delay-200"
                                x-transition:enter-start="scale-0"
                                x-transition:enter-end="scale-100">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-stone-800">{{ __('Payment Complete') }}</h3>
                        <p class="text-xs text-stone-400 mt-1">{{ __('Order has been processed successfully') }}</p>
                    </div>

                    <div id="receipt-print-area" class="bg-stone-50 rounded-xl p-3 font-mono text-xs leading-relaxed whitespace-pre-wrap text-stone-700 border border-stone-100 max-h-48 overflow-y-auto">
                        {{ $receiptContent }}
                    </div>

                    <div class="flex gap-2.5 mt-4">
                        <button wire:click="printReceipt"
                            class="flex-1 px-3 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl font-semibold transition-colors flex items-center justify-center gap-1.5 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/></svg>
                            {{ __('Print') }}
                        </button>
                        <button wire:click="newOrder"
                            class="flex-1 px-3 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white rounded-xl font-semibold transition-all shadow-sm shadow-amber-200/50 text-sm flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
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
            :class="{
                'bg-green-600 text-white': type === 'success',
                'bg-red-600 text-white': type === 'error',
                'bg-blue-600 text-white': type === 'info',
                'bg-amber-500 text-white': type === 'warning'
            }">
            <template x-if="type === 'success'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </template>
            <template x-if="type === 'error'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </template>
            <template x-if="type === 'info'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </template>
            <template x-if="type === 'warning'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </template>
            <span x-text="message"></span>
        </div>
    </div>
</div>
