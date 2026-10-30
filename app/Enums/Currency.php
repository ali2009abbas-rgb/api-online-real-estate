<?php
namespace App\Enums;

enum Currency: string
{
    case USD = 'USD';
    case SYP = 'SYP';
    case EUR = 'EUR';

    public function symbol(): string
    {
        return match($this) {
            self::USD => '$',
            self::SYP => 'ل.س',
            self::EUR => '€',
        };
    }
}
