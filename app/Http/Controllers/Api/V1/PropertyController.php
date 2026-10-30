<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PropertyFilterRequest;
use App\Services\PropertyService;
use App\Http\Resources\V1\PropertyResource;
use App\Http\Resources\V1\PropertyDetailsResource;
use App\Services\CacheService;
use Illuminate\Http\JsonResponse;

class PropertyController extends Controller
{
    protected PropertyService $propertyService;

    public function __construct(PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
    }

    /**
     * قائمة العقارات مع الفلاتر والتقسيم
     */
    public function index(PropertyFilterRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $perPage = $filters['per_page'] ?? 15;

        $properties = $this->propertyService->listProperties($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => PropertyResource::collection($properties),
            'meta' => [
                'current_page' => $properties->currentPage(),
                'last_page' => $properties->lastPage(),
                'per_page' => $properties->perPage(),
                'total' => $properties->total(),
            ]
        ]);
    }

    /**
     * العقارات المميزة (مكاشة عبر Redis)
     */
    public function featured(): JsonResponse
    {
        $properties = $this->propertyService->getFeaturedProperties('v1');

        return response()->json([
            'success' => true,
            'data' => PropertyResource::collection($properties)
        ]);
    }

    /**
     * عدد العقارات المتاحة بكل محافظة (مكاشة عبر Redis)
     */
    public function governoratesStats(): JsonResponse
    {
        $stats = $this->propertyService->getGovernoratesStats('v1');

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * تفاصيل عقار معهدة للنسخة الأولى
     */
    public function show(int $id): JsonResponse
    {
        $isAuthorized = request()->user() !== null;
        $property = $this->propertyService->getPropertyDetails($id, 'v1', $isAuthorized);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'العقار غير موجود أو غير متاح.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new PropertyDetailsResource($property)
        ]);
    }
}
