
                @foreach(array_filter(config('app.supported_locales', []), fn ($l) => $l !== app()->getLocale()) as $code)
                    <a href="{{ route('language.switch', $code) }}"
                        class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition-colors hover:bg-gray-50 dark:hover:bg-gray-700">
                        <x-heroicon-o-language class="w-5 h-5 text-gray-400" />
                        <span>{{ $code === 'en' ? __('English') : __(ucfirst($code)) }}</span>
                    </a>
                @endforeach
            