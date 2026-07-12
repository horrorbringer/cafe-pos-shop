@use('App\Domain\Shared\Enums\OrderStatus')

<div class="min-h-screen bg-gradient-to-br from-stone-900 via-stone-800 to-stone-900 text-white flex flex-col"
    wire:poll.3s
    x-data="{ prevCount: {{ $this->activeOrder?->items->count() ?? 0 }} }"
    x-init="setInterval(() => {
        let newCount = {{ $this->activeOrder?->items->count() ?? 0 }};
        if (newCount > prevCount) {
            $refs.itemsList?.scrollTo({ top: $refs.itemsList.scrollHeight, behavior: 'smooth' });
        }
        prevCount = newCount;
    }, 3000)">

    {{-- Header --}}
    <div class="px-8 py-5 flex items-center justify-between border-b border-white/10 shrink-0">
        <div>
            <h1 class="text-2xl font-bold tracking-tight">{{ $this->settings['store_name'] }}</h1>
            <p class="text-sm text-white/50 mt-0.5">{{ now()->format('l, M d, Y • h:i A') }}</p>
        </div>
        <div class="text-right">
            @if($this->activeOrder)
                <span class="text-2xl font-mono font-bold text-amber-400">{{ $this->activeOrder->order_number }}</span>
            @else
                <span class="text-lg text-white/40">{{ __('No active order') }}</span>
            @endif
        </div>
    </div>

    @if($this->activeOrder)
        {{-- Order Type & Status --}}
        <div class="px-8 py-3 flex items-center gap-3 border-b border-white/10 shrink-0">
            <span class="px-3 py-1 rounded-full text-sm font-medium
                @if($this->activeOrder->status === OrderStatus::Draft) bg-stone-600 text-white
                @elseif($this->activeOrder->status === OrderStatus::Pending) bg-yellow-500 text-black
                @elseif($this->activeOrder->status === OrderStatus::Paid) bg-green-500 text-white
                @else bg-stone-600 text-white @endif">
                {{ $this->activeOrder->status->label() }}
            </span>
            <span class="text-sm text-white/60">{{ $this->activeOrder->order_type->label() }}</span>
            @if($this->activeOrder->table_number)
                <span class="text-sm text-white/60">&middot; {{ __('Table') }} {{ $this->activeOrder->table_number }}</span>
            @endif
            <span class="text-sm text-white/60 ml-auto">{{ $this->activeOrder->items->count() }} {{ __('item(s)') }}</span>
        </div>

        {{-- Items List --}}
        <div class="flex-1 overflow-y-auto px-8 py-4 space-y-2" x-ref="itemsList">
            @foreach($this->activeOrder->items as $item)
                <div class="bg-white/10 rounded-2xl px-6 py-4 flex items-center gap-5 transition-all"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-8"
                    x-transition:enter-end="opacity-100 translate-x-0">
                    {{-- Quantity Badge --}}
                    <div class="w-14 h-14 rounded-full bg-amber-500/20 border-2 border-amber-400/40 flex items-center justify-center shrink-0">
                        <span class="text-2xl font-bold text-amber-400">{{ $item->quantity }}</span>
                    </div>

                    {{-- Item Details --}}
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xl font-bold leading-tight">{{ $item->product_name }}</h3>
                        @if($item->variant_name)
                            <p class="text-base text-white/50 mt-0.5">{{ $item->variant_name }}</p>
                        @endif
                        @if($item->modifiers->isNotEmpty())
                            <div class="flex flex-wrap gap-1.5 mt-2">
                                @foreach($item->modifiers as $modifier)
                                    <span class="px-2.5 py-0.5 rounded-full text-sm bg-white/10 text-white/70">
                                        {{ $modifier->modifier_option_name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                        @if($item->notes)
                            <p class="text-sm text-white/40 italic mt-1">&ldquo;{{ $item->notes }}&rdquo;</p>
                        @endif
                    </div>

                    {{-- Price --}}
                    <div class="text-right shrink-0">
                        <p class="text-xl font-bold text-amber-400">${{ number_format($item->total_price, 2) }}</p>
                        @if($item->quantity > 1)
                            <p class="text-sm text-white/40">${{ number_format($item->unit_price, 2) }} {{ __('each') }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Footer Summary --}}
        <div class="border-t border-white/10 px-8 py-5 space-y-2 shrink-0 bg-white/5">
            <div class="flex justify-between text-lg">
                <span class="text-white/60">{{ __('Subtotal') }}</span>
                <span class="font-semibold">${{ number_format($this->activeOrder->subtotal, 2) }}</span>
            </div>
            @if($this->activeOrder->discount > 0)
                <div class="flex justify-between text-lg text-green-400">
                    <span>{{ __('Discount') }}</span>
                    <span class="font-semibold">-${{ number_format($this->activeOrder->discount, 2) }}</span>
                </div>
            @endif
            <div class="flex justify-between text-lg">
                <span class="text-white/60">{{ __('Tax') }}</span>
                <span class="font-semibold">${{ number_format($this->activeOrder->tax, 2) }}</span>
            </div>
            <div class="flex justify-between pt-3 border-t border-white/10">
                <span class="text-2xl font-bold">{{ __('Total') }}</span>
                <span class="text-3xl font-bold text-amber-400">${{ number_format($this->activeOrder->total, 2) }}</span>
            </div>
            @if($this->activeOrder->status === OrderStatus::Paid)
                <div class="mt-3 text-center">
                    <span class="inline-flex items-center gap-2 px-6 py-2 rounded-full bg-green-500/20 text-green-400 text-xl font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ __('Paid') }}
                    </span>
                </div>
            @endif
        </div>
    @else
        {{-- Waiting State --}}
        <div class="flex-1 flex flex-col items-center justify-center text-center px-8">
            <div class="w-24 h-24 rounded-full bg-white/5 flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-white/60">{{ __('Welcome') }}</h2>
            <p class="text-lg text-white/30 mt-2 max-w-md">{{ __('Your order will appear here once the cashier starts ringing it up.') }}</p>
        </div>
    @endif
</div>
