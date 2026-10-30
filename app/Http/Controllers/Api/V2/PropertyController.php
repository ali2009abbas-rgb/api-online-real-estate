<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\PropertyFilterRequest;
use App\Services\PropertyService;
use App\Http\Resources\V2\PropertyResource;
use App\Http\Resources\V2\PropertyDetailsResource;
use Illuminate\Http\JsonResponse;

class PropertyController extends Controller
{
    protected PropertyService $propertyService;

    public function __construct(PropertyService $propertyService)
    {
        $this->propertyService = $propertyService;
    }

    public function index(PropertyFilterRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $perPage = $filters['per_page'] ?? 15;

        $properties = $this->propertyService->listProperties($filters, $perPage);

        return response()->json([
            'success' => true,
            'version' => 'v2',
            'data' => PropertyResource::collection($properties),
            'meta' => [
                'current_page' => $properties->currentPage(),
                'last_page' => $properties->lastPage(),
                'per_page' => $properties->perPage(),
                'total' => $properties->total(),
            ]
        ]);
    }

    public function featured(): JsonResponse
    {
        $properties = $this->propertyService->getFeaturedProperties('v2');

        return response()->json([
            'success' => true,
            'version' => 'v2',
            'data' => PropertyResource::collection($properties)
        ]);
    }

    public function governoratesStats(): JsonResponse
    {
        $stats = $this->propertyService->getGovernoratesStats('v2');

        return response()->json([
            'success' => true,
            'version' => 'v2',
            'data' => $stats
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $isAuthorized = request()->user() !== null;
        $property = $this->propertyService->getPropertyDetails($id, 'v2', $isAuthorized);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'العقار غير موجود أو غير متاح.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'version' => 'v2',
            'data' => new PropertyDetailsResource($property)
        ]);
    }
}
