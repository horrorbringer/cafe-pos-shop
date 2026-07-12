<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Categories\Tables\CategoriesTable;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Models\Category;
use App\Models\Product;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Attributes\Url;

class MenuManagement extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    #[Url(as: 'tab')]
    public ?string $activeTab = 'products';

    protected string $view = 'filament.pages.menu-management';

    protected static ?string $navigationLabel = 'Menu';

    protected static ?int $navigationSort = 1;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['admin', 'manager']) ?? false;
    }

    public function getTitle(): string
    {
        return __('Menu Management');
    }

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Heroicon::OutlinedBookOpen;
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Menu';
    }

    public function table(Table $table): Table
    {
        $tab = strtolower($this->activeTab ?? '') === 'categories' ? 'categories' : 'products';

        return match ($tab) {
            'categories' => CategoriesTable::configure($table->query(Category::query())),
            default => ProductsTable::configure($table->query(Product::query())),
        };
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('addProduct')
                ->label('Add Product')
                ->icon(Heroicon::OutlinedPlus)
                ->url(ProductResource::getUrl('create'))
                ->openUrlInNewTab()
                ->visible(fn () => $this->activeTab !== 'categories'),

            CreateAction::make('addCategory')
                ->label('Add Category')
                ->icon(Heroicon::OutlinedPlus)
                ->url(CategoryResource::getUrl('create'))
                ->openUrlInNewTab()
                ->visible(fn () => $this->activeTab === 'categories'),
        ];
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
    }
}
