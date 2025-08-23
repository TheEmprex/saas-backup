<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'video_url',
        'content',
        'duration_minutes',
        'order',
        'is_active',
        'prerequisites'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'prerequisites' => 'array'
    ];

    /**
     * Get the user progress records for this module.
     */
    public function userProgress(): HasMany
    {
        return $this->hasMany(UserTrainingProgress::class);
    }

    /**
     * Get the tests associated with this module.
     */
    public function tests(): HasMany
    {
        return $this->hasMany(TrainingTest::class);
    }

    /**
     * Scope a query to only include active modules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order modules by their order field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Check if this module has prerequisites.
     */
    public function hasPrerequisites(): bool
    {
        $prereq = $this->prerequisites;
        return is_array($prereq) && !empty($prereq);
    }

    /**
     * Get prerequisite modules.
     */
    public function prerequisiteModules()
    {
        if (!$this->hasPrerequisites()) {
            return collect();
        }

        return self::whereIn('id', $this->prerequisites)->get();
    }
}
