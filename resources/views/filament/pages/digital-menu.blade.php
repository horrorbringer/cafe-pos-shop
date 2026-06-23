<div class="flex gap-4 h-[calc(100vh-10rem)]" x-data="{ copied: false }" x-on:copy-to-clipboard.window="navigator.clipboard.writeText($event.detail.url); copied = true; setTimeout(() => copied = false, 2000)">

    {{-- Left: Editor --}}
    <div class="w-80 shrink-0 flex flex-col gap-3 overflow-y-auto">

        {{-- QR Code --}}
        <div class="filament-card rounded-xl bg-white p-4 shadow-sm dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-semibold text-gray-950 dark:text-white">{{ __('QR Code') }}</h3>
                <x-heroicon-o-qr-code class="w-4 h-4 text-gray-400" />
            </div>
            @if($this->qrCodeDataUri)
                <div class="flex justify-center mb-3">
                    <img src="{{ $this->qrCodeDataUri }}" alt="QR Code" class="w-32 h-32 rounded-lg">
                </div>
            @endif
            @if($this->menuUrl)
                <div class="flex items-center gap-1.5">
                    <input type="text" value="{{ $this->menuUrl }}" readonly
                        class="flex-1 px-2 py-1.5 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-lg text-xs text-gray-600 dark:text-gray-400 font-mono truncate">
                    <button wire:click="copyUrl"
                        class="shrink-0 px-2.5 py-1.5 bg-primary-600 hover:bg-primary-500 text-white text-xs font-medium rounded-lg transition-colors">
                        <span x-show="!copied">{{ __('Copy') }}</span>
                        <span x-show="copied" x-cloak>{{ __('Copied!') }}</span>
                    </button>
                </div>
            @endif
        </div>

        {{-- Appearance --}}
        <div class="filament-card rounded-xl bg-white p-4 shadow-sm dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-semibold text-gray-950 dark:text-white">{{ __('Appearance') }}</h3>
                <x-heroicon-o-paint-brush class="w-4 h-4 text-gray-400" />
            </div>
            <div class="space-y-3">
                @foreach($this->getFormSchema() as $field)
                    @if($field->getName() !== 'isEnabled')
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ $field->getLabel() }}</label>
                            @if($field->getName() === 'menuTitle')
                                <input type="text" wire:model.live="menuTitle" wire:blur="saveTitle"
                                    placeholder="{{ config('app.name', 'POS Cafe') }}"
                                    class="w-full px-2.5 py-1.5 border border-gray-200 dark:border-white/10 rounded-lg text-sm bg-white dark:bg-white/5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            @elseif($field->getName() === 'menuSubtitle')
                                <textarea wire:model.live="menuSubtitle" wire:blur="saveSubtitle" rows="2"
                                    placeholder="Welcome! Scan to order."
                                    class="w-full px-2.5 py-1.5 border border-gray-200 dark:border-white/10 rounded-lg text-sm bg-white dark:bg-white/5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 resize-none"></textarea>
                            @elseif($field->getName() === 'primaryColor')
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="primaryColor" wire:blur="saveColor"
                                        class="w-8 h-8 rounded-lg border border-gray-200 dark:border-white/10 cursor-pointer">
                                    <input type="text" wire:model.live="primaryColor" wire:blur="saveColor"
                                        class="flex-1 px-2.5 py-1.5 border border-gray-200 dark:border-white/10 rounded-lg text-sm font-mono bg-white dark:bg-white/5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                </div>
                            @elseif($field->getName() === 'logoPath')
                                <div wire:ignore>
                                    @if($logoPath)
                                        <div class="relative mb-2">
                                            <img src="{{ asset('storage/' . $logoPath) }}" class="h-10 object-contain rounded">
                                            <button wire:click="removeLogo"
                                                class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600">
                                                &times;
                                            </button>
                                        </div>
                                    @endif
                                    <label class="flex items-center justify-center w-full h-16 border-2 border-dashed border-gray-200 dark:border-white/10 rounded-lg cursor-pointer hover:border-gray-400 dark:hover:border-white/20 transition-colors">
                                        <div class="text-center">
                                            <x-heroicon-o-photo class="w-5 h-5 mx-auto text-gray-400 mb-0.5" />
                                            <span class="text-[10px] text-gray-400">{{ __('Upload logo') }}</span>
                                        </div>
                                        <input type="file" accept="image/*" class="hidden"
                                            x-ref="logoInput"
                                            x-on:change="
                                                const file = $refs.logoInput.files[0];
                                                if (file) {
                                                    const fd = new FormData();
                                                    fd.append('file', file);
                                                    fd.append('_token', document.querySelector('meta[name=csrf-token]').content);
                                                    fetch('/admin/upload/menu-logo', { method: 'POST', body: fd })
                                                        .then(r => r.json())
                                                        .then(d => {
                                                            $wire.set('logoPath', d.path || d.full_path);
                                                            $wire.call('saveLogo');
                                                        });
                                                }
                                            ">
                                    </label>
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach

                {{-- Enable Toggle --}}
                <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-white/5">
                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('Enable Menu') }}</span>
                    <button wire:click="$set('isEnabled', {{ $isEnabled ? 'false' : 'true' }}); saveEnabled"
                        class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors {{ $isEnabled ? 'bg-primary-600' : 'bg-gray-300 dark:bg-gray-600' }}">
                        <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform {{ $isEnabled ? 'translate-x-4' : 'translate-x-0.5' }}"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Social Media --}}
        <div class="filament-card rounded-xl bg-white p-4 shadow-sm dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-semibold text-gray-950 dark:text-white">{{ __('Social Media') }}</h3>
                <x-heroicon-o-share class="w-4 h-4 text-gray-400" />
            </div>
            <div class="space-y-4">
                @php
                    $socialPlatforms = [
                        ['handle' => 'socialFacebook', 'prefix' => 'facebook.com/'],
                        ['handle' => 'socialInstagram', 'prefix' => 'instagram.com/'],
                        ['handle' => 'socialTiktok', 'prefix' => 'tiktok.com/@'],
                        ['handle' => 'socialYoutube', 'prefix' => 'youtube.com/@'],
                        ['handle' => 'socialTelegram', 'prefix' => 't.me/'],
                        ['handle' => 'socialTwitter', 'prefix' => 'x.com/'],
                    ];
                @endphp
                @foreach($socialPlatforms as $platform)
                    <div class="flex items-center gap-0 rounded-lg border border-gray-200 dark:border-white/10 overflow-hidden focus-within:ring-2 focus-within:ring-primary-500 focus-within:border-primary-500">
                        <span class="px-2.5 py-1.5 text-xs text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-white/5 truncate shrink-0">{{ $platform['prefix'] }}</span>
                        <input type="text" wire:model.live="{{ $platform['handle'] }}" wire:blur="saveSocialLinks"
                            placeholder="yourhandle"
                            class="flex-1 min-w-0 px-2.5 py-1.5 text-sm bg-white dark:bg-white/5 border-0 focus:ring-0">
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Right: Live Preview --}}
    <div class="flex-1 filament-card rounded-xl bg-white shadow-sm dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-4 py-2 border-b border-gray-100 dark:border-white/5">
            <div class="flex items-center gap-2">
                <x-heroicon-o-eye class="w-4 h-4 text-gray-400" />
                <h3 class="text-xs font-semibold text-gray-950 dark:text-white">{{ __('Live Preview') }}</h3>
            </div>
            <a href="{{ url('/menu') }}" target="_blank" class="text-xs text-primary-600 hover:text-primary-500 font-medium flex items-center gap-1">
                {{ __('Open full page') }}
                <x-heroicon-o-arrow-top-right-on-square class="w-3 h-3" />
            </a>
        </div>
        <div class="flex-1 bg-gray-100 dark:bg-black/20 p-3">
            <iframe src="{{ url('/menu') }}" class="w-full h-full border-0 rounded-lg bg-white" loading="lazy"></iframe>
        </div>
    </div>
</div>
