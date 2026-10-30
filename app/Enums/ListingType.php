<?php
namespace App\Enums;

enum ListingType: string
{
    case SALE = 'sale';
    case RENT = 'rent';

    public function label(): string
    {
        return match($this) {
            self::SALE => 'للبيع',
            self::RENT => 'للإيجار',
        };
    }
}
