<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserType;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CriticalDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder ensures all critical data is present for the application to function properly.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding critical application data...');
        
        // Seed User Types
        $this->seedUserTypes();
        
        // Seed Subscription Plans
        $this->seedSubscriptionPlans();
        
        // Seed Admin User
        $this->seedAdminUser();
        
        $this->command->info('✅ Critical data seeding completed!');
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
    
    private function seedAdminUser(): void
    {
        $this->command->info('Seeding admin user...');
        
        // Check if admin user already exists by email or username
        $existingUser = User::where('email', 'admin@onlyverified.io')
                           ->orWhere('username', 'admin')
                           ->first();
        
        if ($existingUser) {
            // Update existing user
            $existingUser->update([
                'name' => 'OnlyVerified Admin',
                'email' => 'admin@onlyverified.io',
                'username' => 'admin',
                'password' => Hash::make('AdminMaxou2025!'),
                'avatar' => 'demo/default.png',
                'verified' => 1,
                'email_verified_at' => now(),
                'updated_at' => now(),
            ]);
            $adminUser = $existingUser;
        } else {
            // Create new admin user
            $adminUser = User::create([
                'name' => 'OnlyVerified Admin',
                'email' => 'admin@onlyverified.io',
                'username' => 'admin',
                'password' => Hash::make('AdminMaxou2025!'),
                'avatar' => 'demo/default.png',
                'verified' => 1,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Assign admin role if it exists
        if (class_exists('\Spatie\Permission\Models\Role')) {
            try {
                $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
                $adminUser->assignRole($adminRole);
            } catch (\Exception $e) {
                $this->command->warn('Could not assign admin role: ' . $e->getMessage());
            }
        }
        
        $this->command->info('✓ Admin user seeded (admin@onlyverified.io / AdminMaxou2025!)');
    }
}
