<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrafficType extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get active traffic types.
     */
    public static function getActive()
    {
        return self::where('is_active', true)->orderBy('name')->get();
    }

    /**
     * Get default traffic types for seeding.
     */
    public static function getDefaults()
    {
        return [
            ['name' => 'Organic Social Media', 'category' => 'social', 'description' => 'Instagram, TikTok, Twitter organic posts'],
            ['name' => 'Paid Social Media', 'category' => 'social', 'description' => 'Instagram, TikTok, Twitter paid ads'],
            ['name' => 'Reddit Marketing', 'category' => 'social', 'description' => 'Reddit posts and community engagement'],
            ['name' => 'OnlyFans Promotion', 'category' => 'platform', 'description' => 'Cross-promotion on OnlyFans'],
            ['name' => 'Fansly Promotion', 'category' => 'platform', 'description' => 'Cross-promotion on Fansly'],
            ['name' => 'Cam Site Traffic', 'category' => 'platform', 'description' => 'Chaturbate, Stripchat, etc.'],
            ['name' => 'Dating Apps', 'category' => 'dating', 'description' => 'Tinder, Bumble, Hinge'],
            ['name' => 'Adult Dating Sites', 'category' => 'dating', 'description' => 'AdultFriendFinder, etc.'],
            ['name' => 'Google Ads', 'category' => 'paid', 'description' => 'Google advertising campaigns'],
            ['name' => 'YouTube Content', 'category' => 'content', 'description' => 'YouTube channel traffic'],
            ['name' => 'Blog/Website SEO', 'category' => 'content', 'description' => 'SEO-driven website traffic'],
            ['name' => 'Email Marketing', 'category' => 'email', 'description' => 'Email list campaigns'],
            ['name' => 'Affiliate Marketing', 'category' => 'affiliate', 'description' => 'Affiliate program traffic'],
            ['name' => 'Discord/Telegram', 'category' => 'messaging', 'description' => 'Community-based messaging platforms'],
            ['name' => 'TikTok Live', 'category' => 'live', 'description' => 'TikTok live streaming'],
            ['name' => 'Instagram Live', 'category' => 'live', 'description' => 'Instagram live streaming'],
            ['name' => 'Snapchat', 'category' => 'social', 'description' => 'Snapchat content and promotion'],
            ['name' => 'Pinterest', 'category' => 'social', 'description' => 'Pinterest content marketing'],
            ['name' => 'Twitch', 'category' => 'streaming', 'description' => 'Twitch streaming platform'],
            ['name' => 'Other', 'category' => 'other', 'description' => 'Other traffic sources']
        ];
    }
}
