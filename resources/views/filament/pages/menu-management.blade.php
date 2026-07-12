<x-filament-panels::page>
    <div class="space-y-6">
        <div class="flex items-center gap-4 border-b border-gray-200 dark:border-gray-700 pb-3">
            <button type="button"
                wire:click="switchTab('products')"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-all
                    {{ $activeTab === 'products' ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                <x-filament::icon icon="heroicon-o-cube" class="w-5 h-5" />
                <span>{{ __('Products') }}</span>
                <span class="text-xs px-1.5 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700">{{ \App\Models\Product::count() }}</span>
            </button>
            <button type="button"
                wire:click="switchTab('categories')"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg transition-all
                    {{ $activeTab === 'categories' ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                <x-filament::icon icon="heroicon-o-rectangle-stack" class="w-5 h-5" />
                <span>{{ __('Categories') }}</span>
                <span class="text-xs px-1.5 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700">{{ \App\Models\Category::count() }}</span>
            </button>
        </div>

        {{ $this->table }}
    </div>
</x-filament-panels::page>
