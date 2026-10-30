<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Enums\ListingType;

class PropertyFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // السماح للجميع بالبحث وتصفح العقارات المتاحة
    }

    public function rules(): array
    {
        return [
            'governorate_id' => ['nullable', 'exists:governorates,id'],
            'listing_type' => ['nullable', 'string', 'in:' . ListingType::SALE->value . ',' . ListingType::RENT->value],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'gte:min_price'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'], // حد أعلى لحجم الصفحة كما طلب التاسك
            'cursor' => ['nullable', 'string'], // يدعم الـ scroll اللانهائي للموبايل
        ];
    }

    public function messages(): array
    {
        return [
            'governorate_id.exists' => 'المحافظة المحددة غير موجودة في النظام.',
            'listing_type.in' => 'نوع العرض غير صالح (يجب أن يكون للبيع أو للإيجار).',
            'min_price.numeric' => 'السعر الأدنى يجب أن يكون رقماً.',
            'max_price.numeric' => 'السعر الأقصى يجب أن يكون رقماً.',
            'max_price.gte' => 'السعر الأقصى يجب أن يكون أكبر من أو مساوياً للسعر الأدنى.',
            'per_page.max' => 'حجم الصفحة المسموح به يجب ألا يتجاوز 50 عقاراً في الطلب الواحد.',
            'per_page.integer' => 'حجم الصفحة يجب أن يكون رقماً صحيحاً.',
        ];
    }

    /**
     * تخصيص الاستجابة للخطأ لتتوافق مع شكل الـ JSON الموحد للـ API
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'خطأ في بيانات المدخلات (Validation Error)',
            'errors' => $validator->errors()
        ], 422));
    }
}
