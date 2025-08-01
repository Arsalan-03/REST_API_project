<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Building;
use Illuminate\Http\JsonResponse;

class BuildingController extends Controller
{
    /**
     * Список всех зданий
     */
    public function index(): JsonResponse
    {
        $buildings = Building::with(['organizations.phones', 'organizations.activities'])->get();

        $result = $buildings->map(function ($building) {
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
        });

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    /**
     * Информация о здании по ID
     */
    public function show(int $id): JsonResponse
    {
        $building = Building::with(['organizations.phones', 'organizations.activities'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $building->id,
                'address' => $building->address,
                'latitude' => $building->latitude,
                'longitude' => $building->longitude,
                'organizations' => $building->organizations->map(function ($organization) {
                    return [
                        'id' => $organization->id,
                        'name' => $organization->name,
                        'phones' => $organization->getPhonesArray(),
                        'activities' => $organization->getActivitiesArray(),
                    ];
                })
            ]
        ]);
    }
} 