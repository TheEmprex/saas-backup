<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update existing plans or create if not present
        SubscriptionPlan::updateOrCreate([
            'name' => 'Free'
        ], [
            'description' => 'Perfect for getting started',
            'price' => 0.00,
            'job_post_limit' => 3,
            'chat_application_limit' => 10,
            'unlimited_chats' => false,
            'advanced_filters' => false,
            'analytics' => false,
            'priority_listings' => false,
            'featured_status' => false,
        ]);

        SubscriptionPlan::updateOrCreate([
            'name' => 'Basic'
        ], [
            'description' => 'Great for small teams and growing businesses',
            'price' => 59.00,
            'job_post_limit' => 15,
            'chat_application_limit' => 50,
            'unlimited_chats' => false,
            'advanced_filters' => true,
            'analytics' => true,
            'priority_listings' => true,
            'featured_status' => false,
        ]);

        SubscriptionPlan::updateOrCreate([
            'name' => 'Pro'
        ], [
            'description' => 'Everything you need to scale your business',
            'price' => 99.00,
            'job_post_limit' => null, // Unlimited
            'chat_application_limit' => null, // Unlimited
            'unlimited_chats' => true,
            'advanced_filters' => true,
            'analytics' => true,
            'priority_listings' => true,
            'featured_status' => true,
        ]);

        SubscriptionPlan::updateOrCreate([
            'name' => 'Enterprise'
        ], [
            'description' => 'For large organizations with custom needs',
            'price' => 199.00,
            'job_post_limit' => null, // Unlimited
            'chat_application_limit' => null, // Unlimited
            'unlimited_chats' => true,
            'advanced_filters' => true,
            'analytics' => true,
            'priority_listings' => true,
            'featured_status' => true,
        ]);
    }
}
