<x-filament-panels::page>
    <form wire:submit="save">
        <div class="space-y-6">
            {{ $this->content }}

            <div class="flex items-center justify-between gap-4 bg-white dark:bg-gray-900 rounded-xl p-4 ring-1 ring-gray-950/5 dark:ring-white/10">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Changes are saved immediately across all POS terminals.
                </p>
                <x-filament::button type="submit" size="lg" icon="heroicon-m-check">
                    Save Settings
                </x-filament::button>
            </div>
        </div>
    </form>
</x-filament-panels::page>
