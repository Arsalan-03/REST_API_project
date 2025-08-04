<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreActivityRequest;
use App\Http\Requests\Api\UpdateActivityRequest;
use App\Services\ActivityService;
use Illuminate\Http\JsonResponse;

class ActivityController extends Controller
{
    private ActivityService $activityService;

    public function __construct(ActivityService $activityService)
    {
        $this->activityService = $activityService;
    }

    /**
     * Список всех деятельностей с иерархией
     */
    public function index(): JsonResponse
    {
        $activities = $this->activityService->getAllWithHierarchy();

        return response()->json([
            'success' => true,
            'data' => $activities
        ]);
    }

    /**
     * Информация о деятельности по ID
     */
    public function show(int $id): JsonResponse
    {
        $activity = $this->activityService->getById($id);

        return response()->json([
            'success' => true,
            'data' => $activity
        ]);
    }

    /**
     * Дочерние деятельности
     */
    public function children(int $parentId): JsonResponse
    {
        $activities = $this->activityService->getChildren($parentId);

        return response()->json([
            'success' => true,
            'data' => $activities
        ]);
    }

    /**
     * Создать деятельность
     */
    public function store(StoreActivityRequest $request): JsonResponse
    {
        try {
            $activity = $this->activityService->create($request->validated());

            return response()->json([
                'success' => true,
                'data' => $activity,
                'message' => 'Деятельность создана успешно'
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Обновить деятельность
     */
    public function update(UpdateActivityRequest $request, int $id): JsonResponse
    {
        try {
            $activity = $this->activityService->update($id, $request->validated());

            return response()->json([
                'success' => true,
                'data' => $activity,
                'message' => 'Деятельность обновлена успешно'
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Удалить деятельность
     */
    public function destroy(int $id): JsonResponse
    {
        $this->activityService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Деятельность удалена успешно'
        ]);
    }
} 