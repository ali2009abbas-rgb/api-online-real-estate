<?php
namespace App\Enums;

enum PropertyStatus: string
{
    case AVAILABLE = 'available';
    case RESERVED = 'reserved';
    case SOLD = 'sold';

    public function label(): string
    {
        return match($this) {
            self::AVAILABLE => 'متاح',
            self::RESERVED => 'محجوز',
            self::SOLD => 'مباع',
        };
    }
}
