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
        // Agency subscription plans
        SubscriptionPlan::create([
            'name' => 'Agency Free',
            'description' => 'Basic plan for agencies to get started',
            'price' => 0.00,
            'job_post_limit' => 3,
            'chat_application_limit' => 10,
            'unlimited_chats' => false,
            'advanced_filters' => false,
            'analytics' => false,
            'priority_listings' => false,
            'featured_status' => false,
        ]);

        SubscriptionPlan::create([
            'name' => 'Agency Pro',
            'description' => 'Professional plan for growing agencies',
            'price' => 39.00,
            'job_post_limit' => 15,
            'chat_application_limit' => 50,
            'unlimited_chats' => false,
            'advanced_filters' => true,
            'analytics' => true,
            'priority_listings' => true,
            'featured_status' => false,
        ]);

        SubscriptionPlan::create([
            'name' => 'Agency Premium',
            'description' => 'Premium plan for established agencies',
            'price' => 99.00,
            'job_post_limit' => null, // Unlimited
            'chat_application_limit' => null, // Unlimited
            'unlimited_chats' => true,
            'advanced_filters' => true,
            'analytics' => true,
            'priority_listings' => true,
            'featured_status' => true,
        ]);

        // Chatter subscription plans
        SubscriptionPlan::create([
            'name' => 'Chatter Free',
            'description' => 'Free plan for chatters',
            'price' => 0.00,
            'job_post_limit' => 0,
            'chat_application_limit' => null, // Unlimited applications
            'unlimited_chats' => false,
            'advanced_filters' => false,
            'analytics' => false,
            'priority_listings' => false,
            'featured_status' => false,
        ]);

        SubscriptionPlan::create([
            'name' => 'Chatter Pro',
            'description' => 'Professional plan for chatters (future)',
            'price' => 19.00,
            'job_post_limit' => 0,
            'chat_application_limit' => null, // Unlimited applications
            'unlimited_chats' => true,
            'advanced_filters' => true,
            'analytics' => true,
            'priority_listings' => true,
            'featured_status' => false,
        ]);
    }
}
