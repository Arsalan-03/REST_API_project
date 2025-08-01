<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Building;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrganizationController extends Controller
{
    /**
     * Список всех организаций в конкретном здании
     */
    public function getByBuilding(int $buildingId): JsonResponse
    {
        $building = Building::with(['organizations.phones', 'organizations.activities'])->findOrFail($buildingId);
        
        $organizations = $building->organizations->map(function ($organization) {
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
        });

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
        $activity = Activity::findOrFail($activityId);
        $organizations = $activity->getAllOrganizations()->load(['phones', 'activities', 'building']);

        $result = $organizations->map(function ($organization) {
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
        });

        return response()->json([
            'success' => true,
            'data' => $result
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

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $radius = $request->radius;

        $buildings = Building::with(['organizations.phones', 'organizations.activities'])
            ->get()
            ->filter(function ($building) use ($latitude, $longitude, $radius) {
                return $building->distanceTo($latitude, $longitude) <= $radius;
            });

        $organizations = collect();
        foreach ($buildings as $building) {
            foreach ($building->organizations as $organization) {
                $organizations->push([
                    'id' => $organization->id,
                    'name' => $organization->name,
                    'phones' => $organization->getPhonesArray(),
                    'activities' => $organization->getActivitiesArray(),
                    'building' => [
                        'id' => $building->id,
                        'address' => $building->address,
                        'latitude' => $building->latitude,
                        'longitude' => $building->longitude,
                        'distance' => round($building->distanceTo($latitude, $longitude), 2)
                    ]
                ]);
            }
        }

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

        $buildings = Building::with(['organizations.phones', 'organizations.activities'])
            ->whereBetween('latitude', [$request->min_lat, $request->max_lat])
            ->whereBetween('longitude', [$request->min_lng, $request->max_lng])
            ->get();

        $organizations = collect();
        foreach ($buildings as $building) {
            foreach ($building->organizations as $organization) {
                $organizations->push([
                    'id' => $organization->id,
                    'name' => $organization->name,
                    'phones' => $organization->getPhonesArray(),
                    'activities' => $organization->getActivitiesArray(),
                    'building' => [
                        'id' => $building->id,
                        'address' => $building->address,
                        'latitude' => $building->latitude,
                        'longitude' => $building->longitude,
                    ]
                ]);
            }
        }

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
        $organization = Organization::with(['phones', 'activities', 'building'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
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
            ]
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

        $organizations = Organization::with(['phones', 'activities', 'building'])
            ->where('name', 'like', '%' . $request->name . '%')
            ->get();

        $result = $organizations->map(function ($organization) {
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
        });

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }
} 