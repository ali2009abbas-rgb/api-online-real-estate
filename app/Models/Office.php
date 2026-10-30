<?php

namespace App\Models;

use App\Models\Concerns\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Office extends Model
{
    protected $fillable = ['user_id', 'governorate_id', 'name', 'phone'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    // علاقة الـ Polymorphic لشعار المكتب
    public function logo(): MorphOne
    {
        return $this->morphOne(Media::class, 'mediable');
    }
}
