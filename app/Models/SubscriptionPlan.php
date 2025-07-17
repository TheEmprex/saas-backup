<?php

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

    public function userSubscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    public static function freePlans()
    {
        return self::where('price', 0.0)->get();
    }

    public static function agencyPlans()
    {
        return self::where('name', 'like', '%agency%')->get();
    }

    public static function chatterPlans()
    {
        return self::where('name', 'like', '%chatter%')->get();
    }
}
