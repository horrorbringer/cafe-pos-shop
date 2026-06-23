<?php

namespace App\Domain\Payment\Models;

use App\Domain\Ordering\Models\Payment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentProvider extends Model
{
    protected $fillable = [
        'code',
        'name',
        'is_active',
        'config',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'encrypted:array',
        'settings' => 'array',
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'provider_code', 'code');
    }

    public static function getByCode(string $code): ?static
    {
        return static::where('code', $code)->where('is_active', true)->first();
    }
}
