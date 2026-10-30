<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    // مدة الكاش العقاري (مثلاً: 60 دقيقة للعقارات المميزة والمحافظات، أو حسب الحاجة)
    const TTL_FEATURED = 3600;
    const TTL_STATS = 3600;
    const TTL_PROPERTY = 1800;

    /**
     * مفتاح كاش العقارات المميزة مع فصل النسخ v1 و v2
     */
    public static function getFeaturedKey(string $version): string
    {
        return "properties:featured:{$version}";
    }

    /**
     * مفتاح كاش إحصائيات المحافظات مع فصل النسخ
     */
    public static function getGovernoratesStatsKey(string $version): string
    {
        return "governorates:stats:{$version}";
    }

    /**
     * مفتاح كاش تفاصيل العقار مع تحديد رقم النسخة والعقار
     */
    public static function getPropertyDetailsKey(int $propertyId, string $version): string
    {
        return "property:{$propertyId}:{$version}";
    }

    /**
     * مفتاح تجميعي لمحافظة معينة لتسهيل مسحه دفعة واحدة باستخدام Tags أو Pattern في Redis
     */
    public static function getGovernoratePatternKey(int $governorateId): string
    {
        return "governorate:{$governorateId}:*";
    }

    /**
     * تفريغ كاش العقار ومحافظته فوراً عند التعديل أو الحذف
     */
public static function clearPropertyCache(int $propertyId, int $governorateId): void
    {
        // مسح كاش تفاصيل العقار لكل النسخ
        Cache::forget(self::getPropertyDetailsKey($propertyId, 'v1'));
        Cache::forget(self::getPropertyDetailsKey($propertyId, 'v2'));

        // مسح كاش العقارات المميزة والإحصائيات
        Cache::forget(self::getFeaturedKey('v1'));
        Cache::forget(self::getFeaturedKey('v2'));
        Cache::forget(self::getGovernoratesStatsKey('v1'));
        Cache::forget(self::getGovernoratesStatsKey('v2'));

        // استخدام اتصال Redis بشكل صريح وآمن لتجنب أي تحذيرات في أنواع البيانات
        try {
            $redis = Redis::connection();
            $keys = $redis->keys("property:*:*");

            if (!empty($keys) && is_array($keys)) {
                $prefix = config('database.redis.options.prefix', '');
                foreach ($keys as $key) {
                    $cleanKey = $prefix ? str_replace($prefix, '', $key) : $key;
                    $redis->del($cleanKey);
                }
            }
        } catch (\Exception $e) {
            // في حال لم يتوفر اتصال Redis مفعل بشكل تام في البيئة المحلية التجريبية
        }
    }
}
