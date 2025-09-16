<?php

declare(strict_types=1);

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
        'active',
    ];

    protected $casts = [
        'required_fields' => 'array',
        'requires_kyc' => 'boolean',
        'active' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function userProfiles(): HasMany
    {
        return $this->hasMany(UserProfile::class);
    }
}
