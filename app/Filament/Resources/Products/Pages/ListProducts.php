<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected ?string $heading = 'Products';

    protected ?string $subheading = 'Manage your cafe menu — add, edit, and organize products.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Add Product'),
        ];
    }
}
