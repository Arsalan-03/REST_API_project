<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrganizationController;
use App\Http\Controllers\Api\BuildingController;
use App\Http\Controllers\Api\ActivityController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Middleware для API
Route::middleware(['api.key', 'api.logging', 'api.rate.limit'])->group(function () {
    
    // Маршруты для организаций
    Route::prefix('organizations')->group(function () {
        Route::get('/building/{buildingId}', [OrganizationController::class, 'getByBuilding']);
        Route::get('/activity/{activityId}', [OrganizationController::class, 'getByActivity']);
        Route::get('/radius', [OrganizationController::class, 'getByRadius']);
        Route::get('/area', [OrganizationController::class, 'getByArea']);
        Route::get('/search', [OrganizationController::class, 'searchByName']);
        Route::get('/search/filters', [OrganizationController::class, 'searchWithFilters']);
        Route::get('/{id}', [OrganizationController::class, 'show']);
    });

    // Маршруты для зданий
    Route::prefix('buildings')->group(function () {
        Route::get('/', [BuildingController::class, 'index']);
        Route::get('/{id}', [BuildingController::class, 'show']);
    });

    // Маршруты для деятельностей
    Route::prefix('activities')->group(function () {
        Route::get('/', [ActivityController::class, 'index']);
        Route::get('/{id}', [ActivityController::class, 'show']);
        Route::get('/{parentId}/children', [ActivityController::class, 'children']);
        Route::post('/', [ActivityController::class, 'store']);
        Route::put('/{id}', [ActivityController::class, 'update']);
        Route::delete('/{id}', [ActivityController::class, 'destroy']);
    });
}); 