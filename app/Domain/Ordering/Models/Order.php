<?php

namespace App\Domain\Ordering\Models;

use App\Domain\Shared\Enums\OrderStatus;
use App\Domain\Shared\Enums\OrderType;
use App\Models\OrderSequence;
use App\Models\User;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected static function newFactory(): OrderFactory
    {
        return OrderFactory::new();
    }

    protected $fillable = [
        'order_number',
        'user_id',
        'branch_id',
        'order_type',
        'table_number',
        'subtotal',
        'discount',
        'tax',
        'total',
        'status',
        'amount_paid',
        'change_amount',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'status' => OrderStatus::class,
        'order_type' => OrderType::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(OrderStatusLog::class);
    }

    public function getorderTypeLabelAttribute(): string
    {
        return match ($this->order_type) {
            'dine_in' => 'Dine-in',
            'takeaway' => 'Takeaway',
            'delivery' => 'Delivery',
            default => ucfirst($this->order_type),
        };
    }

    public static function generateOrderNumber(): string
    {
        $today = now()->format('Ymd');
        $prefix = "ORD-{$today}-";

        return DB::transaction(function () use ($prefix) {
            $sequence = OrderSequence::getNextSequence();

            return $prefix.str_pad($sequence, 4, '0', STR_PAD_LEFT);
        });
    }

    public function canTransitionTo(OrderStatus $target): bool
    {
        return in_array($target, self::allowedTransitions()[$this->status->value] ?? []);
    }

    public static function allowedTransitions(): array
    {
        return [
            OrderStatus::Draft->value => [OrderStatus::Pending, OrderStatus::Cancelled],
            OrderStatus::Pending->value => [OrderStatus::Paid, OrderStatus::Cancelled],
            OrderStatus::Paid->value => [OrderStatus::Completed, OrderStatus::Refunded],
            OrderStatus::Completed->value => [OrderStatus::Refunded],
            OrderStatus::Cancelled->value => [],
            OrderStatus::Refunded->value => [],
        ];
    }
}
