<?php

namespace App\Services;

use App\Models\Organization;
use App\Models\Building;
use App\Models\Activity;
use Illuminate\Support\Collection;

class OrganizationService
{
    /**
     * Получить все организации
     */
    public function getAll(): Collection
    {
        return cache()->remember("organizations_all", 300, function () {
            $organizations = Organization::with(['phones', 'activities', 'building'])->get();
            
            return $organizations->map(function ($organization) {
                return $this->formatOrganization($organization);
            });
        });
    }

    /**
     * Получить организации в здании
     */
    public function getByBuilding(int $buildingId): Collection
    {
        return cache()->remember("organizations_building_{$buildingId}", 300, function () use ($buildingId) {
            $building = Building::with(['organizations.phones', 'organizations.activities'])->findOrFail($buildingId);
            
            return $building->organizations->map(function ($organization) {
                return $this->formatOrganization($organization);
            });
        });
    }

    /**
     * Получить организации по виду деятельности
     */
    public function getByActivity(int $activityId): Collection
    {
        $activity = Activity::findOrFail($activityId);
        $organizations = $activity->getAllOrganizations()->load(['phones', 'activities', 'building']);

        return $organizations->map(function ($organization) {
            return $this->formatOrganization($organization);
        });
    }

    /**
     * Получить организации в радиусе
     */
    public function getByRadius(float $latitude, float $longitude, float $radius): Collection
    {
        $buildings = Building::with(['organizations.phones', 'organizations.activities'])
            ->get()
            ->filter(function ($building) use ($latitude, $longitude, $radius) {
                return $building->distanceTo($latitude, $longitude) <= $radius;
            });

        $organizations = collect();
        foreach ($buildings as $building) {
            foreach ($building->organizations as $organization) {
                $formatted = $this->formatOrganization($organization);
                $formatted['building']['distance'] = round($building->distanceTo($latitude, $longitude), 2);
                $organizations->push($formatted);
            }
        }

        return $organizations;
    }

    /**
     * Получить организации в прямоугольной области
     */
    public function getByArea(float $minLat, float $maxLat, float $minLng, float $maxLng): Collection
    {
        $buildings = Building::with(['organizations.phones', 'organizations.activities'])
            ->whereBetween('latitude', [$minLat, $maxLat])
            ->whereBetween('longitude', [$minLng, $maxLng])
            ->get();

        $organizations = collect();
        foreach ($buildings as $building) {
            foreach ($building->organizations as $organization) {
                $organizations->push($this->formatOrganization($organization));
            }
        }

        return $organizations;
    }

    /**
     * Поиск организаций по названию
     */
    public function searchByName(string $name): Collection
    {
        $organizations = Organization::with(['phones', 'activities', 'building'])
            ->where('name', 'like', '%' . $name . '%')
            ->get();

        return $organizations->map(function ($organization) {
            return $this->formatOrganization($organization);
        });
    }

    /**
     * Получить организацию по ID
     */
    public function getById(int $id): array
    {
        $organization = Organization::with(['phones', 'activities', 'building'])->findOrFail($id);
        return $this->formatOrganization($organization);
    }

    /**
     * Поиск организаций с фильтрацией и сортировкой
     */
    public function searchWithFilters(array $filters = []): Collection
    {
        $query = Organization::with(['phones', 'activities', 'building']);

        // Фильтр по названию
        if (isset($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        // Фильтр по зданию
        if (isset($filters['building_id'])) {
            $query->where('building_id', $filters['building_id']);
        }

        // Фильтр по деятельности
        if (isset($filters['activity_id'])) {
            $query->whereHas('activities', function ($q) use ($filters) {
                $q->where('activities.id', $filters['activity_id']);
            });
        }

        // Сортировка
        $sortBy = $filters['sort_by'] ?? 'name';
        $sortOrder = $filters['sort_order'] ?? 'asc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->get()->map(function ($organization) {
            return $this->formatOrganization($organization);
        });
    }

    /**
     * Форматировать организацию для API ответа
     */
    private function formatOrganization(Organization $organization): array
    {
        return [
            'id' => $organization->id,
            'name' => $organization->name,
            'phones' => $organization->getPhonesArray(),
            'activities' => $organization->getActivitiesArray(),
            'building' => [
                'id' => $organization->building->id,
                'address' => $organization->building->address,
                'latitude' => $organization->building->latitude,
                'longitude' => $organization->building->longitude,
            ]
        ];
    }
} 