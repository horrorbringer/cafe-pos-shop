<?php

namespace App\Domain\Shared\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Card = 'card';
    case Qr = 'qr';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Cash',
            self::Card => 'Card',
            self::Qr => 'QR Payment',
            self::Other => 'Other',
        };
    }
}
