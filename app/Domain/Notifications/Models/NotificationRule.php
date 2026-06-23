<?php

namespace App\Domain\Notifications\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationRule extends Model
{
    protected $table = 'notification_rules';

    protected $fillable = [
        'event_code',
        'channel_code',
        'is_enabled',
        'template_id',
        'cooldown_minutes',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function channel(): BelongsTo
    {
        return $this->belongsTo(NotificationChannel::class, 'channel_code', 'code');
    }

    public function scopeForEvent(Builder $query, string $eventCode): Builder
    {
        return $query->where('event_code', $eventCode);
    }

    public function scopeEnabled(Builder $query): Builder
    {
        return $query->where('is_enabled', true);
    }
}
