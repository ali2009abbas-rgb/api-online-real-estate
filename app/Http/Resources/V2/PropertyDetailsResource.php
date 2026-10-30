<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyDetailsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            // السعر كـ Object في صفحة التفاصيل أيضاً
            'price' => [
                'amount' => (float) $this->price,
                'currency' => $this->currency->value,
                'symbol' => $this->currency->symbol(),
            ],
            'listing_type' => $this->listing_type->value,
            'status' => $this->status->label(),
            'governorate' => $this->governorate->name ?? null,
            'office' => [
                'name' => $this->office->name ?? null,
                'logo' => $this->office->logo?->file_path ?? null,
                'phone' => $request->user() ? $this->office->phone : null,
            ],
            'images' => $this->images->pluck('file_path'),
            'owner' => $this->when($request->user(), function () {
                return [
                    'name' => $this->owner->name ?? null,
                    'phone' => $this->owner->phone ?? null,
                ];
            }),
        ];
    }
}
