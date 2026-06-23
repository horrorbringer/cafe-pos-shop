@props(['dropdown' => false])

@php
    $current = app()->getLocale();
    $locales = [
        'en' => __('English'),
        'km' => __('Khmer'),
    ];
@endphp

@if($dropdown)
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open"
            class="flex items-center gap-1.5 px-2 py-1 rounded-md text-xs font-semibold uppercase tracking-wider border transition-colors"
            :class="darkMode ? 'border-gray-600 text-gray-300' : 'border-gray-200 text-gray-500'">
            <span>{{ strtoupper($current) }}</span>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open" @click.outside="open = false" x-cloak
            class="absolute right-0 mt-1 w-32 rounded-lg shadow-lg border py-1 z-50"
            :class="darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-200'">
            @foreach($locales as $code => $label)
                <a href="{{ route('language.switch', $code) }}"
                    class="block px-3 py-1.5 text-xs font-medium transition-colors"
                    :class="'{{ $current }}' === '{{ $code }}' ? 'text-amber-600 font-bold' : (darkMode ? 'text-gray-300 hover:text-white' : 'text-gray-600 hover:text-gray-900')">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>
@else
    <div class="flex items-center gap-1">
        @foreach($locales as $code => $label)
            <a href="{{ route('language.switch', $code) }}"
                class="px-2 py-1 rounded-md text-xs font-semibold uppercase tracking-wider transition-colors
                {{ $current === $code ? 'bg-amber-100 text-amber-700' : 'text-gray-400 hover:text-gray-600' }}">
                {{ $code === 'en' ? 'EN' : 'KH' }}
            </a>
        @endforeach
    </div>
@endif
