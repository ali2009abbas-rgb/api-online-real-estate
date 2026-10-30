<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'price' => (float) $this->price, // السعر كـ رقم في النسخة الأولى
            'listing_type' => $this->listing_type->value,
            'status' => $this->status->label(),
            'governorate' => $this->governorate->name ?? null,
            'main_image' => $this->images->first()?->file_path ?? null,
            // ملاحظة: العمولة، الملاحظات الداخلية، ورقم التواصل غير موجودة هنا لأنها بطاقة قائمة (أخف)
        ];
    }
}
