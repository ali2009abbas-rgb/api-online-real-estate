<?php

namespace App\Http\Resources\V1;

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
            'price' => (float) $this->price,
            'listing_type' => $this->listing_type->value,
            'status' => $this->status->label(),
            'governorate' => $this->governorate->name ?? null,
            'office' => [
                'name' => $this->office->name ?? null,
                'logo' => $this->office->logo?->file_path ?? null,
                // رقم تواصل المكتب يظهر للمستخدم المسجل دخول فقط
                'phone' => $request->user() ? $this->office->phone : null,
            ],
            'images' => $this->images->pluck('file_path'),

            // شروط الحماية: رقم المالك ورقم التواصل الشخصي يظهر فقط للمستخدم المسجل دخول
            'owner' => $this->when($request->user(), function () {
                return [
                    'name' => $this->owner->name ?? null,
                    'phone' => $this->owner->phone ?? null,
                ];
            }),

            // الحقول الداخلية (العمولة والملاحظات) لا تطلع أبداً لأي مستخدم حسب شروط التاسك
        ];
    }
}
