<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'building_id',
    ];

    /**
     * Здание, в котором находится организация
     */
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    /**
     * Телефоны организации
     */
    public function phones(): HasMany
    {
        return $this->hasMany(OrganizationPhone::class);
    }

    /**
     * Деятельности организации
     */
    public function activities(): BelongsToMany
    {
        return $this->belongsToMany(Activity::class, 'organization_activity');
    }

    /**
     * Получить все телефоны в виде массива
     */
    public function getPhonesArray(): array
    {
        return $this->phones->pluck('phone')->toArray();
    }

    /**
     * Получить все деятельности в виде массива
     */
    public function getActivitiesArray(): array
    {
        return $this->activities->pluck('name')->toArray();
    }
} 