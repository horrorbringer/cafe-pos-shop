<?php

namespace App\Domain\Notifications\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationChannel extends Model
{
    protected $table = 'notification_channels';

    protected $fillable = [
        'code',
        'is_enabled',
        'settings',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'settings' => 'encrypted:array',
    ];

    public function recipients(): HasMany
    {
        return $this->hasMany(NotificationRecipient::class, 'channel_code', 'code');
    }

    public function rules(): HasMany
    {
        return $this->hasMany(NotificationRule::class, 'channel_code', 'code');
    }

    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('is_enabled', true);
    }
}
