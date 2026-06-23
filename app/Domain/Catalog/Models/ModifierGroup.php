<?php

namespace App\Domain\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModifierGroup extends Model
{
    protected $fillable = [
        'name',
        'is_required',
        'max_selections',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'max_selections' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function options(): HasMany
    {
        return $this->hasMany(ModifierOption::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_modifier_groups');
    }
}
