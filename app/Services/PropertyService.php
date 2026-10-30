<?php

namespace App\Services;

use App\Repositories\PropertyRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Property;
use Illuminate\Support\Facades\Cache;

class PropertyService
{
    protected PropertyRepository $propertyRepository;

    public function __construct(PropertyRepository $propertyRepository)
    {
        $this->propertyRepository = $propertyRepository;
    }

    public function listProperties(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        // القوائم الكبيرة وفلاتر البحث المتقدمة لا تخزن بالكامل في الكاش لتعدد تركيباتها،
        // بل تعتمد على الـ Indexes في قاعدة البيانات كما طلب التاسك، بينما التفاصيل المميزة تُكاش.
        return $this->propertyRepository->getPaginatedProperties($filters, $perPage);
    }

    public function getFeaturedProperties(string $version = 'v1'): Collection
    {
        $cacheKey = CacheService::getFeaturedKey($version);

        return Cache::remember($cacheKey, CacheService::TTL_FEATURED, function () {
            return $this->propertyRepository->getFeaturedProperties();
        });
    }

    public function getGovernoratesStats(string $version = 'v1'): Collection
    {
        $cacheKey = CacheService::getGovernoratesStatsKey($version);

        return Cache::remember($cacheKey, CacheService::TTL_STATS, function () {
            return $this->propertyRepository->getAvailableCountPerGovernorate();
        });
    }

    public function getPropertyDetails(int $id, string $version = 'v1', bool $isAuthorized = false): ?Property
    {
        // إذا كان المستخدم مسجلاً دخول، لا نخزن تفاصيل الاتصال بالحساسات في الكاش العام لضمان الخصوصية،
        // أو نعتمد الكاش للبيانات العامة ونستعلم بحذر. هنا نكاشي للزوار.
        if ($isAuthorized) {
            return $this->propertyRepository->findById($id, true);
        }

        $cacheKey = CacheService::getPropertyDetailsKey($id, $version);

        return Cache::remember($cacheKey, CacheService::TTL_PROPERTY, function () use ($id) {
            return $this->propertyRepository->findById($id, false);
        });
    }
}
