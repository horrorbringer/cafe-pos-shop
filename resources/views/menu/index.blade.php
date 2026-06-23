<!DOCTYPE html>
<html lang="en">
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
    </style>
</head>
<body class="bg-gray-50 min-h-screen antialiased" x-data="menuApp()" :class="{ 'bg-gray-900': darkMode }">
    <div class="max-w-lg mx-auto px-4 py-6">

        {{-- Promo Banner --}}
        <div x-show="hasPromo" x-cloak class="mb-5 rounded-xl overflow-hidden" :style="{ background: 'linear-gradient(135deg, ' + primaryColor + ', #f97316)' }">
            <div class="p-4 flex items-center gap-3 text-white">
                <svg class="w-8 h-8 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
                <div>
                    <p class="font-bold text-sm" x-text="promoBannerText"></p>
                    <p class="text-xs opacity-90" x-text="promoBanner"></p>
                </div>
            </div>
        </div>

        {{-- Header --}}
        <div class="text-center mb-6 relative">
            <div class="absolute right-0 top-0 flex items-center gap-1">
                <div x-show="enableKhmer">
                    <button @click="lang = lang === 'en' ? 'km' : 'en'"
                        class="px-2 py-1 rounded-md text-xs font-semibold uppercase tracking-wider border"
                        :class="darkMode ? 'border-gray-600 text-gray-300' : 'border-gray-200 text-gray-500'"
                        x-text="lang === 'en' ? 'KH' : 'EN'">
                    </button>
                </div>
                <button @click="darkMode = !darkMode"
                    class="p-2 rounded-md"
                    :class="darkMode ? 'bg-gray-700 text-white' : 'bg-white text-gray-500'"
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
                    <span :class="isOpen ? 'text-green-600 font-medium' : 'text-red-500 font-medium'">
                        <span x-text="lang === 'en' ? (isOpen ? 'Open' : 'Closed') : (isOpen ? 'បើក' : 'បិទ')"></span>
                    </span>
                    <span class="mx-0.5">&middot;</span>
                    <span x-text="openingHours"></span>
                </span>
            </div>

            <template x-if="tableId">
                <p class="text-xs font-medium mt-2 px-3 py-1 rounded-full inline-block" :style="{ background: primaryColor + '15', color: primaryColor }">
                    <span x-text="lang === 'en' ? 'Table' : 'តុ'"></span> #<span x-text="tableId"></span>
                </p>
            </template>
        </div>

        {{-- Search --}}
        <div class="mb-5 relative">
            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" x-model="search"
                :placeholder="lang === 'en' ? 'Search menu...' : 'ស្វែងរក...'"
                class="w-full pl-10 pr-10 py-3 rounded-xl border-0 text-sm shadow-sm ring-1 ring-inset transition-shadow duration-200 focus:outline-none focus:ring-2"
                :class="darkMode ? 'bg-gray-800 text-white placeholder-gray-500 ring-gray-700 focus:ring-[var(--menu-primary)]' : 'bg-white text-gray-900 placeholder-gray-400 ring-gray-200 focus:ring-[var(--menu-primary)]'"
                :style="{ '--menu-primary': primaryColor }">
            <button x-show="search" @click="search = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-0.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Category Tabs --}}
        <div class="flex gap-2 overflow-x-auto pb-3 mb-5 scrollbar-hide" style="-webkit-overflow-scrolling: touch;">
            <button @click="activeCategory = 'all'"
                class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-all duration-200 shrink-0"
                :class="activeCategory === 'all' ? 'shadow-sm' : ''"
                :style="activeCategory === 'all' ? { background: primaryColor, color: 'white' } : (darkMode ? { background: '#1f2937', color: '#d1d5db' } : { background: 'white', color: '#4b5563' })">
                <span x-text="lang === 'en' ? 'All' : 'ទាំងអស់'"></span>
            </button>
            @foreach($categories as $category)
                <button @click="activeCategory = '{{ $category->slug }}'"
                    class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-all duration-200 shrink-0"
                    :class="activeCategory === '{{ $category->slug }}' ? 'shadow-sm' : ''"
                    :style="activeCategory === '{{ $category->slug }}' ? { background: primaryColor, color: 'white' } : (darkMode ? { background: '#1f2937', color: '#d1d5db' } : { background: 'white', color: '#4b5563' })">
                    {{ $category->icon ?? '☕' }} {{ $category->name }}
                </button>
            @endforeach
        </div>

        {{-- Menu Items --}}
        @foreach($categories as $category)
            <div x-show="activeCategory === 'all' || activeCategory === '{{ $category->slug }}'"
                x-cloak
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="mb-6">

                <div class="flex items-center gap-2 mb-3">
                    <span class="text-lg">{{ $category->icon ?? '☕' }}</span>
                    <h2 class="text-lg font-bold" :class="darkMode ? 'text-white' : 'text-gray-900'">{{ $category->name }}</h2>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium" :class="darkMode ? 'bg-gray-700 text-gray-400' : 'bg-gray-100 text-gray-500'">{{ $category->products->count() }}</span>
                </div>

                @if($category->products->isEmpty())
                    <div class="text-center py-10 rounded-xl" :class="darkMode ? 'bg-gray-800' : 'bg-white'">
                        <p class="text-sm" :class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                            <span x-text="lang === 'en' ? 'No items available in this category' : 'គ្មានមុខម្ហូបក្នុងប្រភេទនេះ'"></span>
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
                                $whatsappNumber = $menuSettings['whatsapp_number'] ?? '';
                                $productImage = $product->image ? asset('storage/' . $product->image) : null;
                            @endphp
                            <div x-show="matchesSearch({{ $product->id }})"
                                x-cloak
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                @click="openModal({{ $product->id }})"
                                class="rounded-xl overflow-hidden cursor-pointer active:scale-[0.98] transition-transform"
                                :class="darkMode ? 'bg-gray-800' : 'bg-white shadow-sm ring-1 ring-gray-200'">

                                <div class="flex">
                                    {{-- Image --}}
                                    <div class="w-28 sm:w-32 shrink-0 relative">
                                        @if($productImage)
                                            <img src="{{ $productImage }}" alt="{{ $product->name }}" class="w-full h-full object-cover" loading="lazy">
                                        @else
                                            <div class="w-full h-full min-h-[7rem] flex items-center justify-center" :class="darkMode ? 'bg-gray-700' : 'bg-gray-50'">
                                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        @if(!$product->is_available)
                                            <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                                <span class="text-white text-xs font-bold uppercase tracking-wider" x-text="lang === 'en' ? 'Sold Out' : 'បានលក់អស់'"></span>
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
                                                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-blue-100 text-blue-700" x-text="lang === 'en' ? 'NEW' : 'ថ្មី'"></span>
                                                        @elseif($tag === 'popular')
                                                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-orange-100 text-orange-700" x-text="lang === 'en' ? 'POPULAR' : 'ពេញនិយម'"></span>
                                                        @elseif($tag === 'signature')
                                                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-purple-100 text-purple-700" x-text="lang === 'en' ? 'SIGNATURE' : 'សញ្ញាណ'"></span>
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

                                        {{-- Bottom: Price + Order --}}
                                        <div class="mt-auto pt-1.5 flex items-center justify-between">
                                            <div class="font-bold text-sm" :style="{ color: primaryColor }">
                                                @if($hasPriceRange)
                                                    ${{ number_format($minPrice, 2) }} &ndash; ${{ number_format($maxPrice, 2) }}
                                                @else
                                                    ${{ number_format($product->price, 2) }}
                                                @endif
                                            </div>
                                            @if($whatsappNumber && $product->is_available)
                                                <a href="https://wa.me/{{ $whatsappNumber }}?text={{ urlencode('I want to order: ' . $product->name) }}" target="_blank"
                                                    class="text-xs font-medium px-3 py-1.5 rounded-lg text-white hover:opacity-90 transition-all flex items-center gap-1.5"
                                                    style="background: #25D366">
                                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                                    <span x-text="lang === 'en' ? 'Order' : 'បញ្ជាទិញ'"></span>
                                                </a>
                                            @endif
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
                                <span x-text="lang === 'en' ? 'No items match your search' : 'គ្មានមុខម្ហូបត្រូវនឹងការស្វែងរក'"></span>
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
            <p class="text-lg font-medium" :class="darkMode ? 'text-gray-300' : 'text-gray-800'" x-text="lang === 'en' ? 'No items found' : 'រកមិនឃើញ'"></p>
            <p class="text-sm mt-1" :class="darkMode ? 'text-gray-500' : 'text-gray-400'" x-text="lang === 'en' ? 'Try a different search term' : 'សូមសាកល្បងពាក្យផ្សេងទៀត'"></p>
        </div>

        {{-- Footer --}}
        <div class="mt-10 text-center pb-8">
            <div class="flex items-center justify-center gap-3 mb-4">
                <button @click="share()"
                    x-show="supportsShare"
                    class="px-4 py-2 rounded-lg text-xs font-medium flex items-center gap-2 border transition-colors"
                    :class="darkMode ? 'border-gray-600 text-gray-300 hover:bg-gray-700' : 'border-gray-200 text-gray-600 hover:bg-gray-50'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                    <span x-text="lang === 'en' ? 'Share Menu' : 'ចែករំលែក'"></span>
                </button>
                <a :href="'https://wa.me/?text=' + encodeURIComponent(title + ' Menu: ' + window.location.href)" target="_blank"
                    class="px-4 py-2 rounded-lg text-xs font-medium flex items-center gap-2 text-white hover:opacity-90 transition-all"
                    style="background: #25D366">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    <span x-text="lang === 'en' ? 'Share via WhatsApp' : 'ចែករំលែកតាម WhatsApp'"></span>
                </a>
            </div>
            <p class="text-xs" :class="darkMode ? 'text-gray-500' : 'text-gray-400'" x-text="lang === 'en' ? 'Scanned via QR' : 'បានស្កេនតាម QR'"></p>
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
                        <span class="text-white text-sm font-bold uppercase tracking-wider" x-text="lang === 'en' ? 'Sold Out' : 'បានលក់អស់'"></span>
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
                    <span x-show="selectedProduct?.has_price_range" x-text="'$' + Number(selectedProduct?.min_price).toFixed(2) + ' – $' + Number(selectedProduct?.max_price).toFixed(2)"></span>
                    <span x-show="!selectedProduct?.has_price_range" x-text="'$' + Number(selectedProduct?.price).toFixed(2)"></span>
                </div>

                {{-- Variants --}}
                <template x-if="selectedProduct?.variants?.length > 1">
                    <div class="mb-4">
                        <h4 class="text-xs font-semibold uppercase tracking-wider mb-2" :class="darkMode ? 'text-gray-400' : 'text-gray-500'" x-text="lang === 'en' ? 'Available Options' : 'ជម្រើស'"></h4>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="variant in selectedProduct.variants" :key="variant.id">
                                <span class="text-sm px-3 py-1.5 rounded-lg font-medium border" :class="darkMode ? 'bg-gray-700 border-gray-600 text-gray-200' : 'bg-gray-50 border-gray-200 text-gray-700'"
                                    x-text="variant.name + ' ($' + Number(variant.price ?? selectedProduct.price).toFixed(2) + ')'"></span>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Order Button --}}
                <button x-show="whatasappNumber && selectedProduct?.is_available"
                    @click="window.open('https://wa.me/' + whatasappNumber + '?text=' + encodeURIComponent('I want to order: ' + selectedProduct.name), '_blank')"
                    class="w-full py-3 rounded-xl text-white font-semibold text-sm flex items-center justify-center gap-2 hover:opacity-90 transition-all active:scale-[0.98]"
                    style="background: #25D366">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    <span x-text="lang === 'en' ? 'Order via WhatsApp' : 'បញ្ជាទិញតាម WhatsApp'"></span>
                </button>

                <p x-show="!whatasappNumber && selectedProduct?.is_available" class="text-center text-sm py-3" :class="darkMode ? 'text-gray-400' : 'text-gray-500'">
                    <span x-text="lang === 'en' ? 'Ask at the counter to place your order' : 'សូមសួរនៅកន្លែងលក់ដើម្បីបញ្ជាទិញ'"></span>
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('menuApp', () => ({
                search: '',
                darkMode: false,
                lang: 'en',
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
                whatasappNumber: @js($menuSettings['whatsapp_number'] ?? ''),
                products: @json($productsJson),
                showModal: false,
                selectedProduct: null,

                openModal(productId) {
                    this.selectedProduct = this.products.find(p => p.id === productId) ?? null;
                    this.showModal = true;
                    document.body.style.overflow = 'hidden';
                },

                closeModal() {
                    this.showModal = false;
                    this.selectedProduct = null;
                    document.body.style.overflow = '';
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
