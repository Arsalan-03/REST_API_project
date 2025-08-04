<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GetByRadiusRequest;
use App\Http\Requests\Api\GetByAreaRequest;
use App\Http\Requests\Api\SearchByNameRequest;
use App\Http\Requests\Api\SearchWithFiltersRequest;
use App\Services\OrganizationService;
use Illuminate\Http\JsonResponse;

class OrganizationController extends Controller
{
    private OrganizationService $organizationService;

    public function __construct(OrganizationService $organizationService)
    {
        $this->organizationService = $organizationService;
    }

    /**
     * Список всех организаций
     */
    public function index(): JsonResponse
    {
        $organizations = $this->organizationService->getAll();

        return response()->json([
            'success' => true,
            'data' => $organizations
        ]);
    }

    /**
     * Список организаций в заданном радиусе
     */
    public function getByRadius(GetByRadiusRequest $request): JsonResponse
    {
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
    public function getByArea(GetByAreaRequest $request): JsonResponse
    {
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
    public function searchByName(SearchByNameRequest $request): JsonResponse
    {
        $organizations = $this->organizationService->searchByName($request->name);

        return response()->json([
            'success' => true,
            'data' => $organizations
        ]);
    }

    /**
     * Поиск организаций с фильтрами
     */
    public function searchWithFilters(SearchWithFiltersRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $organizations = $this->organizationService->searchWithFilters($filters);

        return response()->json([
            'success' => true,
            'data' => $organizations
        ]);
    }
} 