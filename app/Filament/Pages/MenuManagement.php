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
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
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

    public function mount(): void
    {
        $this->authorizeAccess();
    }

    protected function authorizeAccess(): void
    {
        //
    }

    public function getTitle(): string
    {
        return 'Menu Management';
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
        $tab = match (strtolower($this->activeTab ?? '')) {
            'categories' => 'categories',
            default => 'products',
        };

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
                ->openUrlInNewTab(),

            CreateAction::make('addCategory')
                ->label('Add Category')
                ->icon(Heroicon::OutlinedPlus)
                ->url(CategoryResource::getUrl('create'))
                ->openUrlInNewTab(),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->key('menuTabs')
                    ->livewireProperty('activeTab')
                    ->contained(false)
                    ->tabs([
                        Tab::make('Products')
                            ->icon(Heroicon::OutlinedCube)
                            ->badge(fn (): int => Product::count()),
                        Tab::make('Categories')
                            ->icon(Heroicon::OutlinedRectangleStack)
                            ->badge(fn (): int => Category::count()),
                    ]),
                EmbeddedTable::make(),
            ]);
    }
}
