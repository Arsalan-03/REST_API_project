<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BuildingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    private BuildingService $buildingService;

    public function __construct(BuildingService $buildingService)
    {
        $this->buildingService = $buildingService;
    }

    /**
     * Список всех зданий
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 10);
        $buildings = $this->buildingService->getAllPaginated($perPage);

        return response()->json([
            'success' => true,
            'data' => $buildings->items(),
            'pagination' => [
                'current_page' => $buildings->currentPage(),
                'last_page' => $buildings->lastPage(),
                'per_page' => $buildings->perPage(),
                'total' => $buildings->total(),
                'from' => $buildings->firstItem(),
                'to' => $buildings->lastItem(),
            ]
        ]);
    }

    /**
     * Информация о здании по ID
     */
    public function show(int $id): JsonResponse
    {
        $building = $this->buildingService->getById($id);

        return response()->json([
            'success' => true,
            'data' => $building
        ]);
    }
} 