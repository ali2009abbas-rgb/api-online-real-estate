<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            // السعر كـ Object يضم المبلغ والعملة (مطلب أساسي في V2)
            'price' => [
                'amount' => (float) $this->price,
                'currency' => $this->currency->value,
                'symbol' => $this->currency->symbol(),
            ],
            'listing_type' => $this->listing_type->value,
            'status' => $this->status->label(),
            'governorate' => $this->governorate->name ?? null,
            'main_image' => $this->images->first()?->file_path ?? null,
        ];
    }
}
