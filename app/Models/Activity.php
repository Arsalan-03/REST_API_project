<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id',
        'level',
    ];

    protected $casts = [
        'level' => 'integer',
    ];

    /**
     * Родительская деятельность
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Activity::class, 'parent_id');
    }

    /**
     * Дочерние деятельности
     */
    public function children(): HasMany
    {
        return $this->hasMany(Activity::class, 'parent_id');
    }

    /**
     * Все дочерние деятельности (рекурсивно)
     */
    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    /**
     * Организации, занимающиеся этой деятельностью
     */
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'organization_activity');
    }

    /**
     * Получить все дочерние деятельности (включая вложенные)
     */
    public function getAllDescendants(): \Illuminate\Support\Collection
    {
        $descendants = collect();
        
        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getAllDescendants());
        }
        
        return $descendants;
    }

    /**
     * Получить все организации, занимающиеся этой деятельностью и её дочерними
     */
    public function getAllOrganizations(): \Illuminate\Support\Collection
    {
        $activities = collect([$this])->merge($this->getAllDescendants());
        $activityIds = $activities->pluck('id');
        
        return Organization::whereHas('activities', function ($query) use ($activityIds) {
            $query->whereIn('activities.id', $activityIds);
        })->get();
    }
} 