<?php

namespace App\Domain\Shared\Enums;

enum OrderType: string
{
    case DineIn = 'dine_in';
    case Takeaway = 'takeaway';
    case Delivery = 'delivery';

    public function label(): string
    {
        return match ($this) {
            self::DineIn => 'Dine-in',
            self::Takeaway => 'Takeaway',
            self::Delivery => 'Delivery',
        };
    }

    public static function options(): array
    {
        return array_map(fn (OrderType $case) => $case->label(), self::cases());
    }
}
