@props(['dropdown' => false])

@php
    $current = app()->getLocale();
    $locales = collect(config('app.supported_locales', []))->mapWithKeys(fn ($code) => [
        $code => $code === 'en' ? __('English') : __(ucfirst($code)),
    ]);
@endphp

@if($dropdown)
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" @keydown.escape="open = false"
            class="flex items-center gap-1.5 px-2 py-1 rounded-md text-xs font-semibold uppercase tracking-wider border border-gray-200 text-gray-500 dark:border-gray-600 dark:text-gray-300 transition-colors hover:bg-gray-50 dark:hover:bg-gray-700"
            aria-haspopup="true" :aria-expanded="open" aria-label="{{ __('Language') }}">
            <span>{{ strtoupper($current) }}</span>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open" @click.outside="open = false" @keydown.escape="open = false" x-cloak
            class="absolute right-0 mt-1 w-32 rounded-lg shadow-lg border bg-white border-gray-200 dark:bg-gray-800 dark:border-gray-700 py-1 z-50"
            role="menu">
            @foreach($locales as $code => $label)
                <a href="{{ route('language.switch', $code) }}"
                    class="block px-3 py-1.5 text-xs font-medium transition-colors"
                    :class="'{{ $current }}' === '{{ $code }}' ? 'text-amber-600 font-bold' : 'text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white'"
                    role="menuitem" @if($current === $code) aria-current="true" @endif>
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>
@else
    <div class="flex items-center gap-1" role="group" aria-label="{{ __('Language') }}">
        @foreach($locales as $code => $label)
            <a href="{{ route('language.switch', $code) }}"
                class="px-2 py-1 rounded-md text-xs font-semibold uppercase tracking-wider transition-colors
                {{ $current === $code ? 'bg-amber-100 text-amber-700' : 'text-gray-400 hover:text-gray-600' }}"
                @if($current === $code) aria-current="true" @endif
                aria-label="{{ $label }}">
                {{ strtoupper($code) }}
            </a>
        @endforeach
    </div>
@endif
