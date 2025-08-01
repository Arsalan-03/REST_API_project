<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OrganizationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrganizationController extends Controller
{
    private OrganizationService $organizationService;

    public function __construct(OrganizationService $organizationService)
    {
        $this->organizationService = $organizationService;
    }

    /**
     * Список всех организаций в конкретном здании
     */
    public function getByBuilding(int $buildingId): JsonResponse
    {
        $organizations = $this->organizationService->getByBuilding($buildingId);

        return response()->json([
            'success' => true,
            'data' => $organizations
        ]);
    }

    /**
     * Список всех организаций по виду деятельности
     */
    public function getByActivity(int $activityId): JsonResponse
    {
        $organizations = $this->organizationService->getByActivity($activityId);

        return response()->json([
            'success' => true,
            'data' => $organizations
        ]);
    }

    /**
     * Список организаций в заданном радиусе
     */
    public function getByRadius(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'required|numeric|min:0.1|max:100',
        ]);

        $organizations = $this->organizationService->getByRadius(
            $request->latitude,
            $request->longitude,
            $request->radius
        );

        return response()->json([
            'success' => true,
            'data' => $organizations
        ]);
    }

    /**
     * Список организаций в прямоугольной области
     */
    public function getByArea(Request $request): JsonResponse
    {
        $request->validate([
            'min_lat' => 'required|numeric|between:-90,90',
            'max_lat' => 'required|numeric|between:-90,90',
            'min_lng' => 'required|numeric|between:-180,180',
            'max_lng' => 'required|numeric|between:-180,180',
        ]);

        $organizations = $this->organizationService->getByArea(
            $request->min_lat,
            $request->max_lat,
            $request->min_lng,
            $request->max_lng
        );

        return response()->json([
            'success' => true,
            'data' => $organizations
        ]);
    }

    /**
     * Информация об организации по ID
     */
    public function show(int $id): JsonResponse
    {
        $organization = $this->organizationService->getById($id);

        return response()->json([
            'success' => true,
            'data' => $organization
        ]);
    }

    /**
     * Поиск организаций по названию
     */
    public function searchByName(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|min:2',
        ]);

        $organizations = $this->organizationService->searchByName($request->name);

        return response()->json([
            'success' => true,
            'data' => $organizations
        ]);
    }

    /**
     * Поиск организаций с фильтрами
     */
    public function searchWithFilters(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'nullable|string|min:2',
            'building_id' => 'nullable|integer|exists:buildings,id',
            'activity_id' => 'nullable|integer|exists:activities,id',
            'sort_by' => 'nullable|string|in:name,created_at',
            'sort_order' => 'nullable|string|in:asc,desc',
        ]);

        $filters = $request->only(['name', 'building_id', 'activity_id', 'sort_by', 'sort_order']);
        $organizations = $this->organizationService->searchWithFilters($filters);

        return response()->json([
            'success' => true,
            'data' => $organizations
        ]);
    }
} 