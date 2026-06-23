<?php

namespace App\Domain\Catalog\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }

    protected $fillable = [
        'category_id',
        'branch_id',
        'name',
        'slug',
        'description',
        'price',
        'image',
        'stock_quantity',
        'is_available',
        'tags',
        'images',
        'allergens',
        'calories',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_available' => 'boolean',
        'tags' => 'json',
        'images' => 'json',
        'allergens' => 'json',
        'calories' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(ProductIngredient::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    public function modifierGroups(): BelongsToMany
    {
        return $this->belongsToMany(ModifierGroup::class, 'product_modifier_groups');
    }

    public function getMinPriceAttribute(): float
    {
        $minVariant = $this->variants->where('is_active', true)->min('price_adjustment');

        return $this->price + ($minVariant ?? 0);
    }

    public function getMaxPriceAttribute(): float
    {
        $maxVariant = $this->variants->where('is_active', true)->max('price_adjustment');

        return $this->price + ($maxVariant ?? 0);
    }

    public function hasVariants(): bool
    {
        return $this->variants->where('is_active', true)->isNotEmpty();
    }
}
