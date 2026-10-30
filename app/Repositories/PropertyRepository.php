<?php

namespace App\Repositories;

use App\Models\Property;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PropertyRepository
{
    /**
     * جلب العقارات المميزة أو القوائم مع الفلاتر والتقسيم المناسب
     */
    public function getPaginatedProperties(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return Property::query()
            ->with(['governorate', 'office.logo', 'images', 'owner'])
            ->inGovernorate($filters['governorate_id'] ?? null)
            ->ofType($filters['listing_type'] ?? null)
            ->priceRange($filters['min_price'] ?? null, $filters['max_price'] ?? null)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * جلب العقارات المميزة
     */
    public function getFeaturedProperties(int $limit = 5): Collection
    {
        return Property::query()
            ->with(['governorate', 'office.logo', 'images'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * حساب عدد العقارات المتاحة بكل محافظة
     */
    public function getAvailableCountPerGovernorate(): Collection
    {
        return Property::query()
            ->select('governorate_id', DB::raw('count(*) as total'))
            ->groupBy('governorate_id')
            ->with('governorate')
            ->get();
    }

    /**
     * العثور على عقار مع علاقاته (مع مراعاة صلاحيات المكتب ورؤية كل الحالات)
     */
    public function findById(int $id, bool $withAllStatuses = false): ?Property
    {
        $query = Property::with(['governorate', 'office.logo', 'images', 'owner']);

        if ($withAllStatuses) {
            $query->withAllStatuses();
        }

        return $query->find($id);
    }
}
