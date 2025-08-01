<?php

namespace App\Services;

use App\Models\Building;
use Illuminate\Support\Collection;

class BuildingService
{
    /**
     * Получить все здания
     */
    public function getAll(): Collection
    {
        $buildings = Building::with(['organizations.phones', 'organizations.activities'])->get();

        return $buildings->map(function ($building) {
            return $this->formatBuilding($building);
        });
    }

    /**
     * Получить здание по ID
     */
    public function getById(int $id): array
    {
        $building = Building::with(['organizations.phones', 'organizations.activities'])->findOrFail($id);
        return $this->formatBuilding($building);
    }

    /**
     * Получить здания в радиусе
     */
    public function getByRadius(float $latitude, float $longitude, float $radius): Collection
    {
        $buildings = Building::with(['organizations.phones', 'organizations.activities'])
            ->get()
            ->filter(function ($building) use ($latitude, $longitude, $radius) {
                return $building->distanceTo($latitude, $longitude) <= $radius;
            });

        return $buildings->map(function ($building) use ($latitude, $longitude) {
            $formatted = $this->formatBuilding($building);
            $formatted['distance'] = round($building->distanceTo($latitude, $longitude), 2);
            return $formatted;
        });
    }

    /**
     * Получить здания в прямоугольной области
     */
    public function getByArea(float $minLat, float $maxLat, float $minLng, float $maxLng): Collection
    {
        $buildings = Building::with(['organizations.phones', 'organizations.activities'])
            ->whereBetween('latitude', [$minLat, $maxLat])
            ->whereBetween('longitude', [$minLng, $maxLng])
            ->get();

        return $buildings->map(function ($building) {
            return $this->formatBuilding($building);
        });
    }

    /**
     * Получить все здания с пагинацией
     */
    public function getAllPaginated(int $perPage = 10)
    {
        return Building::with(['organizations.phones', 'organizations.activities'])
            ->paginate($perPage);
    }

    /**
     * Форматировать здание для API ответа
     */
    private function formatBuilding(Building $building): array
    {
        return [
            'id' => $building->id,
            'address' => $building->address,
            'latitude' => $building->latitude,
            'longitude' => $building->longitude,
            'organizations_count' => $building->organizations->count(),
            'organizations' => $building->organizations->map(function ($organization) {
                return [
                    'id' => $organization->id,
                    'name' => $organization->name,
                    'phones' => $organization->getPhonesArray(),
                    'activities' => $organization->getActivitiesArray(),
                ];
            })
        ];
    }
} 