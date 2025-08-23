<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserType extends Model
{
    protected $fillable = [
        'name',
        'display_name', 
        'description',
        'required_fields',
        'requires_kyc',
        'can_post_jobs',
        'can_hire_talent',
        'active'
    ];

    protected $casts = [
        'required_fields' => 'array',
        'requires_kyc' => 'boolean',
        'can_post_jobs' => 'boolean',
        'can_hire_talent' => 'boolean',
        'active' => 'boolean'
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function userProfiles(): HasMany
    {
        return $this->hasMany(UserProfile::class);
    }

    /**
     * Check if this user type can post jobs
     */
    public function canPostJobs(): bool
    {
        return $this->can_post_jobs;
    }

    /**
     * Check if this user type can hire talent
     */
    public function canHireTalent(): bool
    {
        return $this->can_hire_talent;
    }

    /**
     * Check if this user type is a hiring entity (agencies or content creators)
     */
    public function isHiringEntity(): bool
    {
        return $this->can_hire_talent;
    }

    /**
     * Check if this user type is a talent (professionals who apply to jobs)
     */
    public function isTalent(): bool
    {
        return !$this->can_hire_talent;
    }

    /**
     * Get all hiring user types
     */
    public static function getHiringTypes()
    {
        return static::where('can_hire_talent', true)->where('active', true)->get();
    }

    /**
     * Get all talent user types
     */
    public static function getTalentTypes()
    {
        return static::where('can_hire_talent', false)->where('active', true)->get();
    }
}
