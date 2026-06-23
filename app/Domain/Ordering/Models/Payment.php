<?php

namespace App\Domain\Ordering\Models;

use App\Domain\Shared\Enums\PaymentMethod;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected static function newFactory(): PaymentFactory
    {
        return PaymentFactory::new();
    }

    protected $fillable = [
        'order_id',
        'provider_code',
        'method',
        'amount',
        'currency',
        'status',
        'reference_number',
        'provider_reference',
        'idempotency_key',
        'provider_payload',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'method' => PaymentMethod::class,
        'provider_payload' => 'array',
        'paid_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function markAsPaid(?string $transactionReference = null, ?\DateTime $paidAt = null): void
    {
        $this->update([
            'status' => 'paid',
            'provider_reference' => $transactionReference ?? $this->provider_reference,
            'paid_at' => $paidAt ?? now(),
        ]);
    }
}
