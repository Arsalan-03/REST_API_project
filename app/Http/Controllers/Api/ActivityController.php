<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
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
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|integer|exists:activities,id',
        ]);

        try {
            $activity = $this->activityService->create($request->all());

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
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'parent_id' => 'sometimes|nullable|integer|exists:activities,id',
        ]);

        try {
            $activity = $this->activityService->update($id, $request->all());

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