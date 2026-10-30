<?php

namespace App\Models;

use App\Enums\PropertyStatus;
use App\Enums\ListingType;
use App\Enums\Currency;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

class Property extends Model
{
    protected $fillable = [
        'office_id', 'owner_id', 'governorate_id', 'title',
        'description', 'price', 'currency', 'listing_type',
        'status', 'commission_percentage'
    ];

    protected $casts = [
        'status' => PropertyStatus::class,
        'listing_type' => ListingType::class,
        'currency' => Currency::class,
        'price' => 'decimal:2',
        'commission_percentage' => 'decimal:2',
    ];

    // Global Scope: الزائر لا يرى إلا العقارات المتاحة (المباعة أو المحجوزة تختفي عنه)، مع إمكانية تجاوز مقصود للمكتب[cite: 3]
    protected static function booted(): void
    {
        static::addGlobalScope('available_for_visitors', function (Builder $builder) {
            // استخدام Auth Facade صراحة لتجنب أي خطأ في Intelephense
            if (!Auth::check() || !request()->routeIs('office.*')) {
                $builder->where('status', PropertyStatus::AVAILABLE->value);
            }
        });
    }

    // Local Scope لتجاوز الـ Global Scope عند طلب المكتب لعقاراته الخاصة
    public function scopeWithAllStatuses(Builder $query): Builder
    {
        return $query->withoutGlobalScope('available_for_visitors');
    }

    // Local Scopes للفلاتر المطلوبة (محافظة، نوع عرض، مجال سعر)[cite: 3]
    public function scopeInGovernorate(Builder $query, ?int $governorateId): Builder
    {
        return $query->when($governorateId, fn($q) => $q->where('governorate_id', $governorateId));
    }

    public function scopeOfType(Builder $query, ?string $type): Builder
    {
        return $query->when($type, fn($q) => $q->where('listing_type', $type));
    }

    public function scopePriceRange(Builder $query, ?float $min, ?float $max): Builder
    {
        return $query->when($min, fn($q) => $q->where('price', '>=', $min))
                     ->when($max, fn($q) => $q->where('price', '<=', $max));
    }

    // العلاقات الأساسية
    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    // علاقة الـ Polymorphic لصور العقار المتعددة
    public function images(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }
}
