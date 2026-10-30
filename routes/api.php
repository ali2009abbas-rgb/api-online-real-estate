<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\PropertyController as PropertyControllerV1;
use App\Http\Controllers\Api\V2\PropertyController as PropertyControllerV2;

/*
|--------------------------------------------------------------------------
| API Routes - Darna Real Estate Platform
|--------------------------------------------------------------------------
*/

// مسارات النسخة الأولى (V1)
Route::prefix('v1')->group(function () {
    Route::get('/properties', [PropertyControllerV1::class, 'index']);
    Route::get('/properties/featured', [PropertyControllerV1::class, 'featured']);
    Route::get('/governorates/stats', [PropertyControllerV1::class, 'governoratesStats']);

    // مسار يحتاج مصادقة Sanctum لاختبار رقم المالك وتواصل المكتب
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/properties/{id}', [PropertyControllerV1::class, 'show']);
    });
    // مسار عام للزوار بدون تفاصيل حساسة (أو يمكن دمجه مع التحقق داخل الـ Resource)
    Route::get('/properties/{id}', [PropertyControllerV1::class, 'show']);
});

// مسارات النسخة الثانية (V2 - السعر Object)
Route::prefix('v2')->group(function () {
    Route::get('/properties', [PropertyControllerV2::class, 'index']);
    Route::get('/properties/featured', [PropertyControllerV2::class, 'featured']);
    Route::get('/governorates/stats', [PropertyControllerV2::class, 'governoratesStats']);

    Route::get('/properties/{id}', [PropertyControllerV2::class, 'show']);
});
