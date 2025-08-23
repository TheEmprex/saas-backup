<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserType;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Models\JobPost;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder creates demo data for the platform including users, jobs, and profiles.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding demo data for OnlyVerified platform...');
        
        // Seed User Types
        $this->seedUserTypes();
        
        // Seed Subscription Plans
        $this->seedSubscriptionPlans();
        
        // Seed Demo Users
        $this->seedDemoUsers();
        
        // Seed Demo Jobs
        $this->seedDemoJobs();
        
        $this->command->info('✅ Demo data seeding completed!');
    }
    
    private function seedUserTypes(): void
    {
        $this->command->info('Seeding user types...');
        
        $userTypes = [
            [
                'name' => 'chatter',
                'display_name' => 'Chatter',
                'description' => 'Individual workers who provide chatting services',
                'requires_kyc' => true,
                'required_fields' => [
                    'kyc_document',
                    'typing_speed',
                    'english_proficiency',
                    'experience_agencies',
                    'availability'
                ],
                'active' => true,
                'can_hire' => false,
                'can_be_hired' => true,
            ],
            [
                'name' => 'ofm_agency',
                'display_name' => 'OFM Agency',
                'description' => 'Agencies that manage OnlyFans models',
                'requires_kyc' => false,
                'required_fields' => [
                    'company_name',
                    'company_description',
                    'results_screenshots',
                    'payment_accounts'
                ],
                'active' => true,
                'can_hire' => true,
                'can_be_hired' => false,
            ],
            [
                'name' => 'chatting_agency',
                'display_name' => 'Chatting Agency',
                'description' => 'Agencies that provide chatting services to OFM agencies',
                'requires_kyc' => false,
                'required_fields' => [
                    'company_name',
                    'company_description',
                    'team_members',
                    'portfolio',
                    'past_clients'
                ],
                'active' => true,
                'can_hire' => true,
                'can_be_hired' => true,
            ]
        ];

        foreach ($userTypes as $userTypeData) {
            UserType::updateOrCreate(
                ['name' => $userTypeData['name']],
                $userTypeData
            );
        }
        
        $this->command->info('✓ User types seeded');
    }
    
    private function seedSubscriptionPlans(): void
    {
        $this->command->info('Seeding subscription plans...');
        
        $plans = [
            [
                'name' => 'Free',
                'description' => 'Perfect for getting started',
                'price' => 0.00,
                'job_post_limit' => 3,
                'chat_application_limit' => 10,
                'unlimited_chats' => false,
                'advanced_filters' => false,
                'analytics' => false,
                'priority_listings' => false,
                'featured_status' => false,
            ],
            [
                'name' => 'Basic',
                'description' => 'Great for small teams and growing businesses',
                'price' => 59.00,
                'job_post_limit' => 15,
                'chat_application_limit' => 50,
                'unlimited_chats' => false,
                'advanced_filters' => true,
                'analytics' => true,
                'priority_listings' => true,
                'featured_status' => false,
            ],
            [
                'name' => 'Pro',
                'description' => 'Everything you need to scale your business',
                'price' => 99.00,
                'job_post_limit' => null, // Unlimited
                'chat_application_limit' => null, // Unlimited
                'unlimited_chats' => true,
                'advanced_filters' => true,
                'analytics' => true,
                'priority_listings' => true,
                'featured_status' => true,
            ],
            [
                'name' => 'Enterprise',
                'description' => 'For large organizations with custom needs',
                'price' => 199.00,
                'job_post_limit' => null, // Unlimited
                'chat_application_limit' => null, // Unlimited
                'unlimited_chats' => true,
                'advanced_filters' => true,
                'analytics' => true,
                'priority_listings' => true,
                'featured_status' => true,
            ]
        ];

        foreach ($plans as $planData) {
            SubscriptionPlan::updateOrCreate(
                ['name' => $planData['name']],
                $planData
            );
        }
        
        $this->command->info('✓ Subscription plans seeded');
    }
    
    private function seedDemoUsers(): void
    {
        $this->command->info('Seeding demo users...');
        
        // Admin User
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@onlyverified.io'],
            [
                'name' => 'OnlyVerified Admin',
                'username' => 'admin',
                'password' => Hash::make('AdminMaxou2025!'),
                'avatar' => 'demo/default.png',
                'verified' => 1,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        // Demo OFM Agency
        $ofmAgency = User::updateOrCreate(
            ['email' => 'agency@onlyverified.io'],
            [
                'name' => 'Elite OFM Agency',
                'username' => 'eliteofm',
                'password' => Hash::make('Demo123!'),
                'avatar' => 'demo/default.png',
                'verified' => 1,
                'email_verified_at' => now(),
                'user_type_id' => UserType::where('name', 'ofm_agency')->first()?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        // Demo Chatter
        $chatter1 = User::updateOrCreate(
            ['email' => 'chatter1@onlyverified.io'],
            [
                'name' => 'Sarah Johnson',
                'username' => 'sarahj_chatter',
                'password' => Hash::make('Demo123!'),
                'avatar' => 'demo/default.png',
                'verified' => 1,
                'email_verified_at' => now(),
                'user_type_id' => UserType::where('name', 'chatter')->first()?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        // Demo Chatter 2
        $chatter2 = User::updateOrCreate(
            ['email' => 'chatter2@onlyverified.io'],
            [
                'name' => 'Emily Rodriguez',
                'username' => 'emily_chat',
                'password' => Hash::make('Demo123!'),
                'avatar' => 'demo/default.png',
                'verified' => 1,
                'email_verified_at' => now(),
                'user_type_id' => UserType::where('name', 'chatter')->first()?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        // Demo Chatting Agency
        $chattingAgency = User::updateOrCreate(
            ['email' => 'chatteam@onlyverified.io'],
            [
                'name' => 'ChatMasters Agency',
                'username' => 'chatmasters',
                'password' => Hash::make('Demo123!'),
                'avatar' => 'demo/default.png',
                'verified' => 1,
                'email_verified_at' => now(),
                'user_type_id' => UserType::where('name', 'chatting_agency')->first()?->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        $this->command->info('✓ Demo users seeded');
    }
    
    private function seedDemoJobs(): void
    {
        $this->command->info('Seeding demo jobs...');
        
        if (!class_exists('\App\Models\JobPost')) {
            $this->command->warn('JobPost model not found, skipping job seeding');
            return;
        }
        
        $ofmAgency = User::where('email', 'agency@onlyverified.io')->first();
        $chattingAgency = User::where('email', 'chatteam@onlyverified.io')->first();
        
        if (!$ofmAgency || !$chattingAgency) {
            $this->command->warn('Agency users not found, skipping job seeding');
            return;
        }
        
        // Job 1: OFM Agency looking for chatters
        JobPost::updateOrCreate(
            ['title' => 'Experienced OnlyFans Chatter Needed'],
            [
                'title' => 'Experienced OnlyFans Chatter Needed',
                'description' => 'We are looking for an experienced chatter to join our OnlyFans management team. You will be responsible for engaging with fans, driving sales, and maintaining relationships.',
                'requirements' => 'Minimum 1 year experience, fluent English, 60+ WPM typing speed, available 6+ hours daily',
                'hourly_rate' => 20.00,
                'rate_type' => 'hourly',
                'contract_type' => 'part_time',
                'experience_level' => 'intermediate',
                'min_typing_speed' => 60,
                'min_english_proficiency' => 4,
                'hours_per_week' => 30,
                'status' => 'active',
                'user_id' => $ofmAgency->id,
                'expires_at' => now()->addDays(30),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        // Job 2: OFM Agency looking for chatting agency
        JobPost::updateOrCreate(
            ['title' => 'Chatting Agency Partnership Opportunity'],
            [
                'title' => 'Chatting Agency Partnership Opportunity',
                'description' => 'Established OFM agency seeking partnership with professional chatting agency. We manage 50+ models and need reliable chatting services.',
                'requirements' => 'Proven track record, team of 10+ chatters, 24/7 availability, experience with adult content',
                'fixed_rate' => 10000.00,
                'rate_type' => 'fixed',
                'contract_type' => 'contract',
                'experience_level' => 'advanced',
                'hours_per_week' => 40,
                'duration_months' => 12,
                'status' => 'active',
                'user_id' => $ofmAgency->id,
                'expires_at' => now()->addDays(15),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        // Job 3: Chatting Agency looking for chatters
        JobPost::updateOrCreate(
            ['title' => 'Join Our Elite Chatting Team'],
            [
                'title' => 'Join Our Elite Chatting Team',
                'description' => 'Top-tier chatting agency is expanding! We offer excellent pay, flexible schedules, and ongoing training. Perfect for experienced chatters.',
                'requirements' => '6 months minimum experience, fast typing, excellent English, reliable internet, professional attitude',
                'hourly_rate' => 24.00,
                'rate_type' => 'hourly',
                'contract_type' => 'full_time',
                'experience_level' => 'intermediate',
                'min_typing_speed' => 50,
                'min_english_proficiency' => 5,
                'hours_per_week' => 40,
                'status' => 'active',
                'user_id' => $chattingAgency->id,
                'expires_at' => now()->addDays(45),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        // Job 4: High-paying premium position
        JobPost::updateOrCreate(
            ['title' => 'Premium VIP Chatter - Top Rates'],
            [
                'title' => 'Premium VIP Chatter - Top Rates',
                'description' => 'Exclusive opportunity for top-tier chatter. Handle VIP clients for our premium OnlyFans models. Highest rates in the industry.',
                'requirements' => '2+ years OnlyFans experience, native English, psychology/sales background preferred, discretion required',
                'hourly_rate' => 42.50,
                'rate_type' => 'hourly',
                'contract_type' => 'part_time',
                'experience_level' => 'advanced',
                'min_typing_speed' => 80,
                'min_english_proficiency' => 5,
                'hours_per_week' => 20,
                'status' => 'active',
                'user_id' => $ofmAgency->id,
                'expires_at' => now()->addDays(7),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
        
        $this->command->info('✓ Demo jobs seeded');
    }
}
