<?php

namespace App\Services;

use App\Models\Activity;
use Illuminate\Support\Collection;

class ActivityService
{
    /**
     * Получить все деятельности с иерархией
     */
    public function getAllWithHierarchy(): Collection
    {
        $activities = Activity::with('children')->whereNull('parent_id')->get();
        
        return $activities->map(function ($activity) {
            return $this->formatActivityWithChildren($activity);
        });
    }

    /**
     * Получить деятельность по ID
     */
    public function getById(int $id): array
    {
        $activity = Activity::with(['children', 'organizations.building'])->findOrFail($id);
        return $this->formatActivity($activity);
    }

    /**
     * Получить дочерние деятельности
     */
    public function getChildren(int $parentId): Collection
    {
        $activities = Activity::with('children')->where('parent_id', $parentId)->get();
        
        return $activities->map(function ($activity) {
            return $this->formatActivityWithChildren($activity);
        });
    }

    /**
     * Получить все дочерние деятельности (рекурсивно)
     */
    public function getAllDescendants(int $parentId): Collection
    {
        $activity = Activity::findOrFail($parentId);
        return $activity->getAllDescendants();
    }

    /**
     * Создать деятельность
     */
    public function create(array $data): Activity
    {
        // Проверяем уровень вложенности
        if (isset($data['parent_id'])) {
            $parent = Activity::findOrFail($data['parent_id']);
            if ($parent->level >= 3) {
                throw new \InvalidArgumentException('Максимальный уровень вложенности - 3');
            }
            $data['level'] = $parent->level + 1;
        } else {
            $data['level'] = 1;
        }

        return Activity::create($data);
    }

    /**
     * Обновить деятельность
     */
    public function update(int $id, array $data): Activity
    {
        $activity = Activity::findOrFail($id);
        
        // Проверяем уровень вложенности при изменении родителя
        if (isset($data['parent_id']) && $data['parent_id'] !== $activity->parent_id) {
            $parent = Activity::findOrFail($data['parent_id']);
            if ($parent->level >= 3) {
                throw new \InvalidArgumentException('Максимальный уровень вложенности - 3');
            }
            $data['level'] = $parent->level + 1;
        }

        $activity->update($data);
        return $activity;
    }

    /**
     * Удалить деятельность
     */
    public function delete(int $id): bool
    {
        $activity = Activity::findOrFail($id);
        return $activity->delete();
    }

    /**
     * Форматировать деятельность для API ответа
     */
    private function formatActivity(Activity $activity): array
    {
        return [
            'id' => $activity->id,
            'name' => $activity->name,
            'level' => $activity->level,
            'parent_id' => $activity->parent_id,
            'organizations_count' => $activity->organizations->count(),
            'organizations' => $activity->organizations->map(function ($organization) {
                return [
                    'id' => $organization->id,
                    'name' => $organization->name,
                    'building' => [
                        'id' => $organization->building->id,
                        'address' => $organization->building->address,
                    ]
                ];
            })
        ];
    }

    /**
     * Форматировать деятельность с дочерними элементами
     */
    private function formatActivityWithChildren(Activity $activity): array
    {
        return [
            'id' => $activity->id,
            'name' => $activity->name,
            'level' => $activity->level,
            'children' => $activity->children->map(function ($child) {
                return $this->formatActivityWithChildren($child);
            })
        ];
    }
} 