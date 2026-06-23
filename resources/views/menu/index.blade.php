<!DOCTYPE html>
<html x-bind:lang="lang">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $menuSettings['title'] ?? config('app.name', 'POS Cafe') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
        .content-visibility-auto { content-visibility: auto; contain-intrinsic-size: 120px; }
        .pb-safe { padding-bottom: env(safe-area-inset-bottom, 0); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen antialiased" x-data="menuApp()" :class="{ 'bg-gray-900': darkMode, 'pb-20': cartCount > 0 }">
    <div class="max-w-lg mx-auto px-4 py-6">

        {{-- Promo Banner --}}
        <div x-show="hasPromo" x-cloak class="mb-6 rounded-xl overflow-hidden shadow-lg" :style="{ background: 'linear-gradient(135deg, ' + primaryColor + ', #f97316)' }">
            <div class="relative p-4 flex items-center gap-3 text-white">
                <div class="absolute top-0 right-0 w-20 h-20 bg-white/5 rounded-bl-full"></div>
                <div class="shrink-0 w-10 h-10 rounded-full bg-white/15 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                    </svg>
                </div>
                <div class="relative z-10">
                    <p class="font-bold text-sm" x-text="promoBannerText"></p>
                    <p class="text-xs opacity-90 mt-0.5" x-text="promoBanner"></p>
                </div>
            </div>
        </div>

        {{-- Header --}}
        <div class="text-center mb-7 relative">
            <div class="absolute right-0 top-0 flex items-center gap-1.5">
                <div x-show="enableKhmer">
                    <button @click="lang = lang === 'en' ? 'km' : 'en'"
                        class="px-2.5 py-1.5 rounded-lg text-[11px] font-bold uppercase tracking-wider border transition-colors"
                        :class="darkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-200 text-gray-500 hover:bg-gray-50'"
                        x-text="lang === 'en' ? 'KH' : 'EN'">
                    </button>
                </div>
                <button @click="darkMode = !darkMode"
                    class="p-2 rounded-lg transition-colors"
                    :class="darkMode ? 'bg-gray-700 text-white hover:bg-gray-600' : 'bg-white text-gray-500 hover:bg-gray-50 shadow-sm'"
                    aria-label="Toggle dark mode">
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </button>
            </div>

            <template x-if="logo">
                <img :src="logo" alt="" class="h-14 mx-auto mb-3 object-contain">
            </template>
            <h1 class="text-2xl font-bold tracking-tight" :class="darkMode ? 'text-white' : 'text-gray-900'" x-text="title"></h1>
            <p x-show="subtitle" class="text-sm mt-1" :class="darkMode ? 'text-gray-400' : 'text-gray-500'" x-text="subtitle"></p>

            <div class="flex items-center justify-center gap-2 mt-2">
                <span class="inline-flex items-center gap-1.5 text-xs" :class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                    <span class="w-1.5 h-1.5 rounded-full" :class="isOpen ? 'bg-green-500' : 'bg-red-500'"></span>
                    <span :class="isOpen ? 'text-green-600 font-medium' : 'text-red-500 font-medium'" x-text="isOpen ? t('Open') : t('Closed')"></span>
                    <span class="mx-0.5">&middot;</span>
                    <span x-text="openingHours"></span>
                </span>
            </div>

            <template x-if="tableId">
                <p class="text-xs font-medium mt-2 px-3 py-1 rounded-full inline-block" :style="{ background: primaryColor + '15', color: primaryColor }">
                    <span x-text="t('Table')"></span> #<span x-text="tableId"></span>
                </p>
            </template>
        </div>

        {{-- Search --}}
        <div class="mb-5 relative group">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 transition-colors duration-200"
                :class="search ? 'text-[var(--menu-primary)]' : 'text-gray-400'"
                fill="none" stroke="currentColor" viewBox="0 0 24 24" :style="{ '--menu-primary': primaryColor }">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" x-model="search"
                :placeholder="t('Search menu...')"
                class="w-full pl-10 pr-10 py-3 rounded-xl border-0 text-sm shadow-sm ring-1 ring-inset transition-all duration-200 focus:outline-none focus:ring-2 focus:shadow-md"
                :class="darkMode ? 'bg-gray-800 text-white placeholder-gray-500 ring-gray-700 focus:ring-[var(--menu-primary)]' : 'bg-white text-gray-900 placeholder-gray-400 ring-gray-200 focus:ring-[var(--menu-primary)]'"
                :style="{ '--menu-primary': primaryColor }">
            <button x-show="search" @click="search = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-0.5 rounded-full hover:bg-gray-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Category Tabs --}}
        <div class="relative mb-5">
            <div class="flex gap-1 overflow-x-auto pb-1 scrollbar-hide border-b" :class="darkMode ? 'border-gray-700' : 'border-gray-100'" style="-webkit-overflow-scrolling: touch;">
                <button @click="activeCategory = 'all'"
                    class="px-3 py-2.5 text-sm font-medium whitespace-nowrap shrink-0 border-b-2 -mb-px transition-all duration-200"
                    :class="activeCategory === 'all' ? '' : 'border-transparent'"
                    :style="activeCategory === 'all' ? { borderBottomColor: primaryColor, color: primaryColor } : (darkMode ? { color: '#9ca3af' } : { color: '#6b7280' })">
                    <span x-text="t('All')"></span>
                </button>
                @foreach($categories as $category)
                    <button @click="activeCategory = '{{ $category->slug }}'"
                        class="px-3 py-2.5 text-sm font-medium whitespace-nowrap shrink-0 border-b-2 -mb-px transition-all duration-200"
                        :class="activeCategory === '{{ $category->slug }}' ? '' : 'border-transparent'"
                        :style="activeCategory === '{{ $category->slug }}' ? { borderBottomColor: primaryColor, color: primaryColor } : (darkMode ? { color: '#9ca3af' } : { color: '#6b7280' })">
                        {{ $category->icon ?? '☕' }} {{ $category->name }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Menu Items --}}
        @foreach($categories as $category)
            <div x-show="activeCategory === 'all' || activeCategory === '{{ $category->slug }}'"
                x-cloak
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="mb-6">

                <div class="flex items-center gap-2 mb-3 px-0.5">
                    <span class="text-lg">{{ $category->icon ?? '☕' }}</span>
                    <h2 class="text-lg font-bold tracking-tight" :class="darkMode ? 'text-white' : 'text-gray-900'">{{ $category->name }}</h2>
                    <span class="text-[11px] px-2 py-0.5 rounded-full font-semibold" :class="darkMode ? 'bg-gray-700 text-gray-400' : 'bg-gray-100 text-gray-500'">{{ $category->products->count() }}</span>
                </div>

                @if($category->products->isEmpty())
                    <div class="text-center py-12 rounded-xl" :class="darkMode ? 'bg-gray-800' : 'bg-white shadow-sm ring-1 ring-gray-100'">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <p class="text-sm" :class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                            <span x-text="t('No items available in this category')"></span>
                        </p>
                    </div>
                @else
                    <div class="grid gap-3">
                        @foreach($category->products as $product)
                            @php
                                $activeVariants = $product->variants->where('is_active', true);
                                $minPrice = $product->min_price;
                                $maxPrice = $product->max_price;
                                $hasPriceRange = $activeVariants->count() > 1 && $minPrice != $maxPrice;
                                $productImage = $product->image ? asset('storage/' . $product->image) : null;
                            @endphp
                            <div x-show="matchesSearch({{ $product->id }})"
                                x-cloak
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                @click="openModal({{ $product->id }})"
                                class="rounded-xl overflow-hidden cursor-pointer active:scale-[0.98] transition-transform content-visibility-auto"
                                :class="darkMode ? 'bg-gray-800' : 'bg-white shadow-sm ring-1 ring-gray-200'">

                                <div class="flex">
                                    {{-- Image --}}
                                    <div class="w-28 sm:w-32 shrink-0 relative">
                                        @if($productImage)
                                            <img src="{{ $productImage }}" alt="{{ $product->name }}" class="w-full h-full object-cover" loading="lazy" srcset="{{ $productImage }}?w=160 160w, {{ $productImage }}?w=320 320w" sizes="(max-width: 640px) 160px, 320px">
                                        @else
                                            <div class="w-full h-full min-h-[7rem] flex items-center justify-center" :class="darkMode ? 'bg-gray-700' : 'bg-gray-50'">
                                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        @if(!$product->is_available)
                                            <div class="absolute inset-0 bg-black/60 flex items-center justify-center backdrop-blur-[1px]">
                                                <span class="text-white text-xs font-bold uppercase tracking-wider px-2 py-1 rounded bg-black/40" x-text="t('Sold Out')"></span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Content --}}
                                    <div class="flex-1 p-3 min-w-0 flex flex-col gap-1">
                                        <div class="flex items-start justify-between gap-2">
                                            <h3 class="font-semibold text-sm leading-tight" :class="darkMode ? 'text-white' : 'text-gray-900'">{{ $product->name }}</h3>
                                            @if($product->tags)
                                                <div class="flex flex-wrap gap-1 shrink-0">
                                                    @foreach((array) $product->tags as $tag)
                                                        @if($tag === 'new')
                                                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-blue-100 text-blue-700" x-text="t('NEW')"></span>
                                                        @elseif($tag === 'popular')
                                                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-orange-100 text-orange-700" x-text="t('POPULAR')"></span>
                                                        @elseif($tag === 'signature')
                                                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-purple-100 text-purple-700" x-text="t('SIGNATURE')"></span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>

                                        @if($product->description)
                                            <p class="text-xs line-clamp-2" :class="darkMode ? 'text-gray-400' : 'text-gray-500'">{{ $product->description }}</p>
                                        @endif

                                        {{-- Allergens & Calories --}}
                                        @if($product->calories || $product->allergens)
                                            <div class="flex items-center gap-2 flex-wrap">
                                                @if($product->calories)
                                                    <span class="text-[10px] font-medium" :class="darkMode ? 'text-gray-400' : 'text-gray-400'">{{ $product->calories }} kcal</span>
                                                @endif
                                                @if($product->allergens)
                                                    @foreach((array) $product->allergens as $allergen)
                                                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-yellow-50 text-yellow-600 border border-yellow-100">{{ $allergen }}</span>
                                                    @endforeach
                                                @endif
                                            </div>
                                        @endif

                                        {{-- Variant Pills --}}
                                        @if($hasPriceRange)
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($activeVariants->take(3) as $variant)
                                                    <span class="text-[10px] px-1.5 py-0.5 rounded font-medium" :class="darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600'">{{ $variant->name }}</span>
                                                @endforeach
                                                @if($activeVariants->count() > 3)
                                                    <span class="text-[10px] px-1.5 py-0.5 rounded font-medium" :class="darkMode ? 'bg-gray-700 text-gray-300' : 'bg-gray-100 text-gray-600'">+{{ $activeVariants->count() - 3 }}</span>
                                                @endif
                                            </div>
                                        @endif

                                        {{-- Bottom: Price & Add --}}
                                        <div class="mt-auto pt-1.5 flex items-center justify-between">
                                            <div class="font-bold text-sm" :style="{ color: primaryColor }">
                                                @if($hasPriceRange)
                                                    ${{ number_format($minPrice, 2) }} &ndash; ${{ number_format($maxPrice, 2) }}
                                                @else
                                                    ${{ number_format($product->price, 2) }}
                                                @endif
                                            </div>
                                            <div class="w-7 h-7 rounded-full flex items-center justify-center transition-all"
                                                :style="{ background: primaryColor + '15', color: primaryColor }">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Search empty state per category --}}
                        <div x-show="search !== '' && !categoryHasProducts('{{ $category->slug }}')"
                            x-cloak
                            class="text-center py-10 rounded-xl"
                            :class="darkMode ? 'bg-gray-800' : 'bg-white'">
                            <p class="text-sm" :class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                                <span x-text="t('No items match your search')"></span>
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach

        {{-- Global no results --}}
        <div x-show="search !== '' && !hasAnyResults"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="text-center py-20">
            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <p class="text-lg font-medium" :class="darkMode ? 'text-gray-300' : 'text-gray-800'" x-text="t('No items found')"></p>
            <p class="text-sm mt-1" :class="darkMode ? 'text-gray-500' : 'text-gray-400'" x-text="t('Try a different search term')"></p>
        </div>

        {{-- Social Media Icons --}}
        <div class="flex items-center justify-center gap-4 mb-6 mt-8">
            <template x-for="link in socialLinksList" :key="link.platform">
                <template x-if="link.url">
                    <a :href="link.url" target="_blank" rel="noopener noreferrer"
                        class="w-10 h-10 rounded-full flex items-center justify-center transition-all hover:scale-110 active:scale-95 shadow-sm"
                        :class="darkMode ? 'bg-gray-700 hover:bg-gray-600' : 'bg-white hover:bg-gray-50 hover:shadow-md'"
                        :style="{ color: link.brandColor }"
                        :aria-label="link.platform">
                        <span x-html="link.icon" class="w-5 h-5"></span>
                    </a>
                </template>
                <template x-if="!link.url">
                    <span
                        class="w-10 h-10 rounded-full flex items-center justify-center transition-all shadow-sm"
                        :class="darkMode ? 'bg-gray-700' : 'bg-white'"
                        :style="{ color: link.brandColor + '60' }">
                        <span x-html="link.icon" class="w-5 h-5"></span>
                    </span>
                </template>
            </template>
        </div>

        {{-- Footer --}}
        <div class="mt-2 text-center pb-8">
            <div class="flex items-center justify-center gap-3 mb-4">
                <button @click="share()"
                    x-show="supportsShare"
                    class="px-4 py-2 rounded-lg text-xs font-medium flex items-center gap-2 border transition-colors"
                    :class="darkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-200 text-gray-600 hover:bg-gray-50'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                    <span x-text="t('Share Menu')"></span>
                </button>
            </div>
            <p class="text-xs" :class="darkMode ? 'text-gray-500' : 'text-gray-400'" x-text="t('Scanned via QR')"></p>
        </div>
    </div>

    {{-- Product Detail Modal --}}
    <div x-show="showModal" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click.self="closeModal()"
        @keydown.escape.window="closeModal()"
        class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm"
        style="overscroll-behavior: contain;">

        <div x-show="showModal" x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-full sm:scale-95 sm:translate-y-0 opacity-0"
            x-transition:enter-end="translate-y-0 sm:scale-100 opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-y-0 sm:scale-100 opacity-100"
            x-transition:leave-end="translate-y-full sm:scale-95 sm:translate-y-0 opacity-0"
            class="w-full sm:max-w-sm bg-white rounded-t-2xl sm:rounded-2xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col"
            :class="darkMode ? 'bg-gray-800' : 'bg-white'">

            {{-- Modal Image --}}
            <div class="relative aspect-[4/3] bg-gray-100 shrink-0" :class="darkMode ? 'bg-gray-700' : 'bg-gray-100'">
                <template x-if="selectedProduct?.image">
                    <img :src="selectedProduct.image" :alt="selectedProduct.name" class="w-full h-full object-cover">
                </template>
                <template x-if="!selectedProduct?.image">
                    <div class="w-full h-full flex items-center justify-center" :class="darkMode ? 'bg-gray-700' : 'bg-gray-50'">
                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </template>
                <button @click="closeModal()" class="absolute top-3 right-3 w-8 h-8 rounded-full bg-black/40 text-white flex items-center justify-center hover:bg-black/60 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <template x-if="selectedProduct && !selectedProduct.is_available">
                    <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                        <span class="text-white text-sm font-bold uppercase tracking-wider" x-text="t('Sold Out')"></span>
                    </div>
                </template>
            </div>

            {{-- Modal Body --}}
            <div class="flex-1 overflow-y-auto p-5">
                <div class="flex items-start justify-between gap-3 mb-2">
                    <h2 class="text-lg font-bold leading-tight" :class="darkMode ? 'text-white' : 'text-gray-900'" x-text="selectedProduct?.name"></h2>
                    <template x-if="selectedProduct?.tags?.length">
                        <div class="flex flex-wrap gap-1 shrink-0">
                            <template x-for="tag in selectedProduct.tags" :key="tag">
                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded"
                                    :class="tag === 'new' ? 'bg-blue-100 text-blue-700' : (tag === 'popular' ? 'bg-orange-100 text-orange-700' : (tag === 'signature' ? 'bg-purple-100 text-purple-700' : ''))"
                                    x-text="tag.toUpperCase()"></span>
                            </template>
                        </div>
                    </template>
                </div>

                <p x-show="selectedProduct?.description" class="text-sm leading-relaxed mb-3" :class="darkMode ? 'text-gray-400' : 'text-gray-500'" x-text="selectedProduct?.description"></p>

                {{-- Allergens & Calories --}}
                <div x-show="selectedProduct?.calories || selectedProduct?.allergens?.length" class="flex items-center gap-2 flex-wrap mb-3">
                    <span x-show="selectedProduct?.calories" class="text-xs font-medium px-2 py-0.5 rounded bg-green-50 text-green-600 border border-green-100" x-text="selectedProduct?.calories + ' kcal'"></span>
                    <template x-for="allergen in (selectedProduct?.allergens || [])" :key="allergen">
                        <span class="text-xs px-2 py-0.5 rounded bg-yellow-50 text-yellow-600 border border-yellow-100" x-text="allergen"></span>
                    </template>
                </div>

                {{-- Price --}}
                <div class="text-2xl font-bold mb-4" :style="{ color: primaryColor }">
                    <span x-text="'$' + Number(selectedUnitPrice).toFixed(2)"></span>
                </div>

                {{-- Variants --}}
                <template x-if="selectedProduct?.variants?.length > 1">
                    <div class="mb-4">
                        <h4 class="text-xs font-semibold uppercase tracking-wider mb-2" :class="darkMode ? 'text-gray-400' : 'text-gray-500'" x-text="t('Size / Type')"></h4>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="variant in selectedProduct.variants" :key="variant.id">
                                <button @click="selectVariant(variant)"
                                    class="text-sm px-3 py-1.5 rounded-lg font-medium border transition-all"
                                    :class="selectedVariant?.id === variant.id
                                        ? 'border-2 shadow-sm'
                                        : ''"
                                    :style="selectedVariant?.id === variant.id
                                        ? { borderColor: primaryColor, color: primaryColor, background: primaryColor + '10' }
                                        : (darkMode ? 'border-gray-600 text-gray-300 bg-gray-700' : 'border-gray-200 text-gray-700 bg-gray-50')"
                                    x-text="variant.name + ' ($' + Number(variant.price).toFixed(2) + ')'">
                                </button>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Modifier Groups --}}
                <template x-for="group in (selectedProduct?.modifier_groups || [])" :key="group.id">
                    <div class="mb-4">
                        <h4 class="text-xs font-semibold uppercase tracking-wider mb-2" :class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                            <span x-text="group.name"></span>
                            <span x-show="group.is_required" class="text-red-400 ml-1">*</span>
                            <span x-show="!group.is_required" class="text-gray-400 text-[10px] font-normal ml-1" x-text="t('(optional)')"></span>
                        </h4>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="option in group.options" :key="option.id">
                                <button @click="toggleModifier(option, group)"
                                    class="text-sm px-3 py-1.5 rounded-lg font-medium border transition-all"
                                    :class="isModifierSelected(option.id)
                                        ? 'border-2 shadow-sm'
                                        : ''"
                                    :style="isModifierSelected(option.id)
                                        ? { borderColor: primaryColor, color: primaryColor, background: primaryColor + '10' }
                                        : (darkMode ? 'border-gray-600 text-gray-300 bg-gray-700' : 'border-gray-200 text-gray-700 bg-gray-50')"
                                    x-text="option.name + (option.price > 0 ? ' (+$' + Number(option.price).toFixed(2) + ')' : '')">
                                </button>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Quantity --}}
                <div class="mb-5">
                    <h4 class="text-xs font-semibold uppercase tracking-wider mb-2" :class="darkMode ? 'text-gray-400' : 'text-gray-500'" x-text="t('Quantity')"></h4>
                    <div class="flex items-center gap-3">
                        <button @click="itemQuantity = Math.max(1, itemQuantity - 1)"
                            class="w-9 h-9 rounded-lg flex items-center justify-center font-bold text-lg transition-colors"
                            :class="darkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                            &minus;
                        </button>
                        <span class="text-xl font-bold min-w-[2rem] text-center" :class="darkMode ? 'text-white' : 'text-gray-900'" x-text="itemQuantity"></span>
                        <button @click="itemQuantity = Math.min(99, itemQuantity + 1)"
                            class="w-9 h-9 rounded-lg flex items-center justify-center font-bold text-lg transition-colors"
                            :class="darkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                            +
                        </button>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="mb-4">
                    <input type="text" x-model="itemNotes"
                        :placeholder="t('Special instructions...')"
                        class="w-full px-3 py-2 rounded-lg text-sm border-0 ring-1 ring-inset transition-shadow duration-200 focus:outline-none focus:ring-2"
                        :class="darkMode ? 'bg-gray-700 text-white placeholder-gray-500 ring-gray-600 focus:ring-[var(--menu-primary)]' : 'bg-gray-50 text-gray-900 placeholder-gray-400 ring-gray-200 focus:ring-[var(--menu-primary)]'"
                        :style="{ '--menu-primary': primaryColor }">
                </div>

                {{-- Add to Cart Button --}}
                <button x-show="isOpen && selectedProduct?.is_available"
                    @click="addToCart()"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm flex items-center justify-center gap-2 hover:opacity-90 transition-all active:scale-[0.98]"
                    :style="{ background: primaryColor }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                    </svg>
                    <span x-text="t('Add to Cart')"></span>
                    <span x-text="'— $' + Number(cartItemTotal).toFixed(2)"></span>
                </button>

                <p x-show="!isOpen && selectedProduct?.is_available" class="text-center text-sm py-3 text-red-500 font-medium">
                    <span x-text="t(&quot;We're closed — come back during opening hours&quot;)"></span>
                </p>
            </div>
        </div>
    </div>

    {{-- Cart Slide-up Panel --}}
    <div x-show="showCart" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click.self="showCart = false"
        @keydown.escape.window="showCart = false"
        class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm"
        style="overscroll-behavior: contain;">

        <div x-show="showCart" x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-full sm:scale-95 sm:translate-y-0 opacity-0"
            x-transition:enter-end="translate-y-0 sm:scale-100 opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-y-0 sm:scale-100 opacity-100"
            x-transition:leave-end="translate-y-full sm:scale-95 sm:translate-y-0 opacity-0"
            class="w-full sm:max-w-sm bg-white sm:rounded-2xl rounded-t-2xl shadow-2xl overflow-hidden max-h-[80vh] flex flex-col"
            :class="darkMode ? 'bg-gray-800' : 'bg-white'">

            {{-- Cart Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b shrink-0" :class="darkMode ? 'border-gray-700' : 'border-gray-100'">
                <h3 class="text-lg font-bold" :class="darkMode ? 'text-white' : 'text-gray-900'">
                    <span x-text="t('Your Order')"></span>
                    <span class="text-sm font-normal ml-1" :class="darkMode ? 'text-gray-400' : 'text-gray-500'" x-text="'(' + cartCount + ')'"></span>
                </h3>
                <div class="flex items-center gap-2">
                    <button @click="clearCart()" class="text-xs font-medium px-2 py-1 rounded-lg transition-colors" :class="darkMode ? 'text-red-400 hover:bg-gray-700' : 'text-red-500 hover:bg-red-50'"
                        x-text="t('Clear')">
                    </button>
                    <button @click="showCart = false" class="w-7 h-7 rounded-full flex items-center justify-center" :class="darkMode ? 'text-gray-400 hover:bg-gray-700' : 'text-gray-400 hover:bg-gray-100'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            {{-- Cart Items --}}
            <div class="flex-1 overflow-y-auto p-5 space-y-3">
                <template x-for="(item, index) in cart" :key="index">
                    <div class="flex items-start gap-3 pb-3" :class="darkMode ? 'border-b border-gray-700' : 'border-b border-gray-100'">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-semibold leading-tight" :class="darkMode ? 'text-white' : 'text-gray-900'" x-text="item.name"></p>
                                <p class="text-sm font-bold shrink-0" :style="{ color: primaryColor }" x-text="'$' + Number(item.total_price).toFixed(2)"></p>
                            </div>
                            <p x-show="item.variant_name" class="text-xs mt-0.5" :class="darkMode ? 'text-gray-400' : 'text-gray-500'" x-text="item.variant_name"></p>
                            <template x-for="mod in (item.modifiers || [])" :key="mod.name">
                                <p class="text-xs" :class="darkMode ? 'text-gray-400' : 'text-gray-500'" x-text="'+ ' + mod.name + (mod.price > 0 ? ' ($' + Number(mod.price).toFixed(2) + ')' : '')"></p>
                            </template>
                            <p x-show="item.notes" class="text-xs italic mt-0.5" :class="darkMode ? 'text-gray-500' : 'text-gray-400'" x-text="'\"' + item.notes + '\"'"></p>
                            <div class="flex items-center gap-2 mt-1.5">
                                <button @click="updateCartQty(index, -1)"
                                    class="w-6 h-6 rounded flex items-center justify-center text-xs font-bold transition-colors"
                                    :class="darkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                                    &minus;
                                </button>
                                <span class="text-sm font-semibold min-w-[1.2rem] text-center" :class="darkMode ? 'text-white' : 'text-gray-900'" x-text="item.quantity"></span>
                                <button @click="updateCartQty(index, 1)"
                                    class="w-6 h-6 rounded flex items-center justify-center text-xs font-bold transition-colors"
                                    :class="darkMode ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                                    +
                                </button>
                                <button @click="removeFromCart(index)" class="ml-auto text-xs px-2 py-0.5 rounded transition-colors" :class="darkMode ? 'text-red-400 hover:bg-gray-700' : 'text-red-500 hover:bg-red-50'"
                                    x-text="t('Remove')">
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <div x-show="cart.length === 0" class="text-center py-10">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                    </svg>
                    <p class="text-sm font-medium" :class="darkMode ? 'text-gray-300' : 'text-gray-800'" x-text="t('Your cart is empty')"></p>
                    <p class="text-xs mt-1" :class="darkMode ? 'text-gray-500' : 'text-gray-400'" x-text="t('Tap items to add them')"></p>
                </div>
            </div>

            {{-- Cart Footer --}}
            <div x-show="cart.length > 0" class="px-5 py-4 border-t shrink-0" :class="darkMode ? 'border-gray-700' : 'border-gray-100'">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium" :class="darkMode ? 'text-gray-300' : 'text-gray-700'" x-text="t('Subtotal')"></span>
                    <span class="text-lg font-bold" :style="{ color: primaryColor }" x-text="'$' + Number(cartSubtotal).toFixed(2)"></span>
                </div>
                <button @click="proceedToCheckout()"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm flex items-center justify-center gap-2 hover:opacity-90 transition-all active:scale-[0.98]"
                    :style="{ background: primaryColor }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span x-text="t('Place Order')"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- Checkout Modal --}}
    <div x-show="showCheckout" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click.self="showCheckout = false"
        @keydown.escape.window="showCheckout = false"
        class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 backdrop-blur-sm"
        style="overscroll-behavior: contain;">

        <div x-show="showCheckout" x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-full sm:scale-95 sm:translate-y-0 opacity-0"
            x-transition:enter-end="translate-y-0 sm:scale-100 opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-y-0 sm:scale-100 opacity-100"
            x-transition:leave-end="translate-y-full sm:scale-95 sm:translate-y-0 opacity-0"
            class="w-full sm:max-w-sm bg-white sm:rounded-2xl rounded-t-2xl shadow-2xl overflow-hidden max-h-[80vh] flex flex-col"
            :class="darkMode ? 'bg-gray-800' : 'bg-white'">

            <div class="flex items-center justify-between px-5 py-4 border-b shrink-0" :class="darkMode ? 'border-gray-700' : 'border-gray-100'">
                <h3 class="text-lg font-bold" :class="darkMode ? 'text-white' : 'text-gray-900'" x-text="t('Checkout')"></h3>
                <button @click="showCheckout = false" class="w-7 h-7 rounded-full flex items-center justify-center" :class="darkMode ? 'text-gray-400 hover:bg-gray-700' : 'text-gray-400 hover:bg-gray-100'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-5 space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" :class="darkMode ? 'text-gray-400' : 'text-gray-500'" x-text="t('Your Name')"></label>
                    <input type="text" x-model="checkoutName"
                        :placeholder="t('Enter your name')"
                        class="w-full px-3 py-2.5 rounded-lg text-sm border-0 ring-1 ring-inset transition-shadow duration-200 focus:outline-none focus:ring-2"
                        :class="darkMode ? 'bg-gray-700 text-white placeholder-gray-500 ring-gray-600 focus:ring-[var(--menu-primary)]' : 'bg-gray-50 text-gray-900 placeholder-gray-400 ring-gray-200 focus:ring-[var(--menu-primary)]'"
                        :style="{ '--menu-primary': primaryColor }">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" :class="darkMode ? 'text-gray-400' : 'text-gray-500'" x-text="t('Table Number')"></label>
                    <input type="text" x-model="checkoutTable"
                        :placeholder="t('Table number (optional)')"
                        class="w-full px-3 py-2.5 rounded-lg text-sm border-0 ring-1 ring-inset transition-shadow duration-200 focus:outline-none focus:ring-2"
                        :class="darkMode ? 'bg-gray-700 text-white placeholder-gray-500 ring-gray-600 focus:ring-[var(--menu-primary)]' : 'bg-gray-50 text-gray-900 placeholder-gray-400 ring-gray-200 focus:ring-[var(--menu-primary)]'"
                        :style="{ '--menu-primary': primaryColor }">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" :class="darkMode ? 'text-gray-400' : 'text-gray-500'" x-text="t('Order Type')"></label>
                    <div class="flex gap-2">
                        <button @click="checkoutOrderType = 'dine_in'"
                            class="flex-1 py-2 rounded-lg text-sm font-medium border transition-all"
                            :class="checkoutOrderType === 'dine_in'
                                ? 'border-2 shadow-sm'
                                : ''"
                            :style="checkoutOrderType === 'dine_in'
                                ? { borderColor: primaryColor, color: primaryColor, background: primaryColor + '10' }
                                : (darkMode ? 'border-gray-600 text-gray-300 bg-gray-700' : 'border-gray-200 text-gray-700 bg-gray-50')">
                            <span x-text="t('Dine In')"></span>
                        </button>
                        <button @click="checkoutOrderType = 'takeaway'"
                            class="flex-1 py-2 rounded-lg text-sm font-medium border transition-all"
                            :class="checkoutOrderType === 'takeaway'
                                ? 'border-2 shadow-sm'
                                : ''"
                            :style="checkoutOrderType === 'takeaway'
                                ? { borderColor: primaryColor, color: primaryColor, background: primaryColor + '10' }
                                : (darkMode ? 'border-gray-600 text-gray-300 bg-gray-700' : 'border-gray-200 text-gray-700 bg-gray-50')">
                            <span x-text="t('Takeaway')"></span>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider mb-1.5" :class="darkMode ? 'text-gray-400' : 'text-gray-500'" x-text="t('Special Instructions')"></label>
                    <textarea x-model="checkoutNotes" rows="2"
                        :placeholder="t('Any special requests...')"
                        class="w-full px-3 py-2.5 rounded-lg text-sm border-0 ring-1 ring-inset transition-shadow duration-200 focus:outline-none focus:ring-2 resize-none"
                        :class="darkMode ? 'bg-gray-700 text-white placeholder-gray-500 ring-gray-600 focus:ring-[var(--menu-primary)]' : 'bg-gray-50 text-gray-900 placeholder-gray-400 ring-gray-200 focus:ring-[var(--menu-primary)]'"
                        :style="{ '--menu-primary': primaryColor }">
                    </textarea>
                </div>

                {{-- Order Summary --}}
                <div class="rounded-xl p-4" :class="darkMode ? 'bg-gray-700/50' : 'bg-gray-50'">
                    <h4 class="text-xs font-semibold uppercase tracking-wider mb-2" :class="darkMode ? 'text-gray-400' : 'text-gray-500'" x-text="t('Order Summary')"></h4>
                    <template x-for="item in cart" :key="item.id">
                        <div class="flex items-start justify-between text-sm py-1">
                            <span :class="darkMode ? 'text-gray-300' : 'text-gray-700'" x-text="item.quantity + 'x ' + item.name"></span>
                            <span class="font-medium" :class="darkMode ? 'text-gray-200' : 'text-gray-900'" x-text="'$' + Number(item.total_price).toFixed(2)"></span>
                        </div>
                    </template>
                    <div class="flex items-center justify-between text-sm font-bold pt-2 mt-2 border-t" :class="darkMode ? 'text-white border-gray-600' : 'text-gray-900 border-gray-200'">
                        <span x-text="t('Total')"></span>
                        <span x-text="'$' + Number(cartSubtotal).toFixed(2)"></span>
                    </div>
                </div>

                <p class="text-center text-sm py-3" :class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                    <span x-text="t('Please ask at the counter to place your order')"></span>
                </p>
            </div>
        </div>
    </div>

    {{-- Sticky Bottom Cart Bar --}}
    <div x-show="cartCount > 0" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
        @click="showCart = true"
        class="fixed bottom-0 left-0 right-0 z-40 cursor-pointer active:scale-[0.99] transition-transform pb-safe shadow-[0_-4px_20px_rgba(0,0,0,0.15)]"
        :style="{ background: primaryColor }">
        <div class="max-w-lg mx-auto px-5 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2 text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/>
                </svg>
                <span class="font-semibold text-sm">
                    <span x-text="cartCount"></span>
                    <span x-text="t(cartCount === 1 ? ' item' : ' items')"></span>
                </span>
            </div>
            <div class="flex items-center gap-3 text-white">
                <span class="font-bold text-base" x-text="'$' + Number(cartSubtotal).toFixed(2)"></span>
                <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('menuApp', () => ({
                search: '',
                darkMode: false,
                lang: 'en',
                translations: {
                    en: @js($enTranslations),
                    km: @js($kmTranslations),
                },
                t(key) { return this.translations[this.lang][key] ?? key; },
                activeCategory: 'all',
                primaryColor: @js($menuSettings['primary_color'] ?? '#f59e0b'),
                title: @js($menuSettings['title'] ?? config('app.name', 'POS Cafe')),
                subtitle: @js($menuSettings['subtitle'] ?? ''),
                logo: @js($menuSettings['logo'] ? asset('storage/'.$menuSettings['logo']) : null),
                openingHours: @js($menuSettings['opening_hours'] ?? ''),
                isOpen: @js($menuSettings['is_open']),
                hasPromo: @js((bool) ($menuSettings['promo_banner'] ?? '')),
                promoBanner: @js($menuSettings['promo_banner'] ?? ''),
                promoBannerText: @js($menuSettings['promo_banner_text'] ?? 'Special Offer!'),
                enableKhmer: @js((bool) ($menuSettings['enable_khmer'] ?? false)),
                tableId: @js($tableId),
                supportsShare: typeof navigator.share === 'function',
                socialLinks: @js($menuSettings['social_links'] ?? []),
                socialIcons: {
                    facebook: '<svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
                    instagram: '<svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>',
                    tiktok: '<svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>',
                    youtube: '<svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
                    telegram: '<svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 000 12a12 12 0 0012 12 12 12 0 0012-12A12 12 0 0012 0a12 12 0 00-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 01.171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>',
                    twitter: '<svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
                },
                products: @json($productsJson),
                showModal: false,
                selectedProduct: null,
                selectedVariant: null,
                selectedModifiers: [],
                itemQuantity: 1,
                itemNotes: '',
                cart: [],
                showCart: false,
                showCheckout: false,
                checkoutName: '',
                checkoutTable: '',
                checkoutOrderType: 'dine_in',
                checkoutNotes: '',

                init() {
                    const saved = localStorage.getItem('menu_cart');
                    if (saved) {
                        try { this.cart = JSON.parse(saved); } catch(e) { this.cart = []; }
                    }
                    if (this.tableId) {
                        this.checkoutTable = this.tableId;
                    }
                },

                saveCart() {
                    localStorage.setItem('menu_cart', JSON.stringify(this.cart));
                },

                openModal(productId) {
                    this.selectedProduct = this.products.find(p => p.id === productId) ?? null;
                    this.selectedVariant = null;
                    this.selectedModifiers = [];
                    this.itemQuantity = 1;
                    this.itemNotes = '';
                    this.showModal = true;
                    document.body.style.overflow = 'hidden';
                },

                closeModal() {
                    this.showModal = false;
                    this.selectedProduct = null;
                    this.selectedVariant = null;
                    this.selectedModifiers = [];
                    this.itemQuantity = 1;
                    this.itemNotes = '';
                    document.body.style.overflow = '';
                },

                selectVariant(variant) {
                    this.selectedVariant = variant;
                },

                toggleModifier(option, group) {
                    const idx = this.selectedModifiers.findIndex(m => m.option_id === option.id);
                    if (idx !== -1) {
                        this.selectedModifiers.splice(idx, 1);
                    } else {
                        const groupMods = this.selectedModifiers.filter(m => m.group_id === group.id);
                        if (groupMods.length >= group.max_selections) {
                            this.selectedModifiers.splice(0, 1);
                        }
                        this.selectedModifiers.push({
                            group_id: group.id,
                            group_name: group.name,
                            option_id: option.id,
                            name: option.name,
                            price: option.price,
                        });
                    }
                },

                isModifierSelected(optionId) {
                    return this.selectedModifiers.some(m => m.option_id === optionId);
                },

                get selectedUnitPrice() {
                    if (!this.selectedProduct) return 0;
                    if (this.selectedVariant) return this.selectedVariant.price;
                    if (this.selectedProduct.has_price_range) return this.selectedProduct.min_price;
                    return this.selectedProduct.price;
                },

                get cartItemTotal() {
                    const base = this.selectedUnitPrice * this.itemQuantity;
                    const modTotal = this.selectedModifiers.reduce((s, m) => s + m.price, 0) * this.itemQuantity;
                    return base + modTotal;
                },

                addToCart() {
                    if (!this.selectedProduct) return;

                    const variant = this.selectedVariant || null;
                    const unitPrice = variant ? variant.price : this.selectedProduct.price;
                    const modTotal = this.selectedModifiers.reduce((s, m) => s + m.price, 0);
                    const totalPrice = (unitPrice + modTotal) * this.itemQuantity;

                    this.cart.push({
                        product_id: this.selectedProduct.id,
                        name: this.selectedProduct.name + (variant ? ' (' + variant.name + ')' : ''),
                        variant_name: variant ? variant.name : null,
                        variant_id: variant ? variant.id : null,
                        unit_price: unitPrice + modTotal,
                        total_price: totalPrice,
                        quantity: this.itemQuantity,
                        modifiers: this.selectedModifiers.map(m => ({ name: m.name, price: m.price })),
                        notes: this.itemNotes || null,
                    });

                    this.saveCart();
                    this.closeModal();
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                    this.saveCart();
                },

                updateCartQty(index, delta) {
                    const item = this.cart[index];
                    const newQty = item.quantity + delta;
                    if (newQty <= 0) {
                        this.removeFromCart(index);
                        return;
                    }
                    item.quantity = newQty;
                    item.total_price = item.unit_price * newQty;
                    this.saveCart();
                },

                clearCart() {
                    this.cart = [];
                    this.saveCart();
                    this.showCart = false;
                },

                proceedToCheckout() {
                    this.showCart = false;
                    this.checkoutName = '';
                    this.checkoutNotes = '';
                    this.showCheckout = true;
                },

                placeOrder() {
                    this.showCheckout = false;
                    this.cart = [];
                    this.saveCart();
                },

                get cartCount() {
                    return this.cart.reduce((sum, item) => sum + item.quantity, 0);
                },

                get cartSubtotal() {
                    return this.cart.reduce((sum, item) => sum + item.total_price, 0);
                },

                matchesSearch(productId) {
                    const product = this.products.find(p => p.id === productId);
                    if (!product) return true;

                    if (this.activeCategory !== 'all' && product.category_slug !== this.activeCategory) {
                        return false;
                    }

                    if (!this.search) return true;

                    const q = this.search.toLowerCase();
                    return product.name.toLowerCase().includes(q)
                        || (product.description && product.description.toLowerCase().includes(q));
                },

                categoryHasProducts(slug) {
                    if (this.search === '') return true;
                    const q = this.search.toLowerCase();
                    return this.products.some(p =>
                        p.category_slug === slug
                        && (p.name.toLowerCase().includes(q)
                            || (p.description && p.description.toLowerCase().includes(q)))
                    );
                },

                get hasAnyResults() {
                    if (!this.search) return true;
                    const q = this.search.toLowerCase();
                    return this.products.some(p =>
                        (this.activeCategory === 'all' || p.category_slug === this.activeCategory)
                        && (p.name.toLowerCase().includes(q)
                            || (p.description && p.description.toLowerCase().includes(q)))
                    );
                },

                get socialLinksList() {
                    const platforms = ['facebook', 'instagram', 'tiktok', 'youtube', 'telegram', 'twitter'];
                    const labels = {
                        facebook: 'Facebook',
                        instagram: 'Instagram',
                        tiktok: 'TikTok',
                        youtube: 'YouTube',
                        telegram: 'Telegram',
                        twitter: 'Twitter / X',
                    };
                    const brandColors = {
                        facebook: '#1877F2',
                        instagram: '#E4405F',
                        tiktok: '#010101',
                        youtube: '#FF0000',
                        telegram: '#0088CC',
                        twitter: '#000000',
                    };
                    return platforms.map(p => ({
                        platform: labels[p],
                        url: this.socialLinks[p] || null,
                        icon: this.socialIcons[p],
                        brandColor: brandColors[p],
                    }));
                },

                share() {
                    navigator.share({
                        title: this.title,
                        url: window.location.href,
                    }).catch(() => {});
                },
            }));
        });
    </script>
</body>
</html>
