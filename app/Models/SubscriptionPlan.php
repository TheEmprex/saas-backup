<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'job_post_limit',
        'chat_application_limit',
        'unlimited_chats',
        'advanced_filters',
        'analytics',
        'priority_listings',
        'featured_status',
    ];

    protected $casts = [
        'price' => 'float',
        'job_post_limit' => 'integer',
        'chat_application_limit' => 'integer',
        'unlimited_chats' => 'boolean',
        'advanced_filters' => 'boolean',
        'analytics' => 'boolean',
        'priority_listings' => 'boolean',
        'featured_status' => 'boolean',
    ];

    public static function freePlans()
    {
        return self::query()->where('price', 0.0)->get();
    }

    public static function agencyPlans()
    {
        return self::query()->where('name', 'like', '%agency%')->get();
    }

    public static function chatterPlans()
    {
        return self::query()->where('name', 'like', '%chatter%')->get();
    }

    public function userSubscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }
}
