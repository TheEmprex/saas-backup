<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserType;

class CleanUserTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing user types
        UserType::truncate();
        
        $userTypes = [
            // HIRING ENTITIES (Can post jobs and hire talent)
            [
                'name' => 'content_creator',
                'display_name' => 'Content Creator',
                'description' => 'OnlyFans content creators looking for management services',
                'can_post_jobs' => true,
                'can_hire_talent' => true,
                'requires_kyc' => false,
                'required_fields' => json_encode([
                    'onlyfans_username',
                    'monthly_revenue_proof',
                    'content_niche'
                ]),
                'active' => true
            ],
            [
                'name' => 'ofm_agency',
                'display_name' => 'OFM Agency',
                'description' => 'OnlyFans management agencies looking to hire skilled professionals',
                'can_post_jobs' => true,
                'can_hire_talent' => true,
                'requires_kyc' => false,
                'required_fields' => json_encode([
                    'company_name',
                    'business_email',
                    'revenue_screenshots',
                    'payment_proof',
                    'team_size'
                ]),
                'active' => true
            ],
            [
                'name' => 'chatting_agency',
                'display_name' => 'Chatting Agency',
                'description' => 'Specialized agencies providing outsourced services to agencies',
                'can_post_jobs' => true,
                'can_hire_talent' => true,
                'requires_kyc' => false,
                'required_fields' => json_encode([
                    'company_name',
                    'team_list',
                    'portfolio',
                    'client_testimonials',
                    'performance_metrics'
                ]),
                'active' => true
            ],
            
            // TALENT (Can only apply to jobs)
            [
                'name' => 'chatter',
                'display_name' => 'Chatter',
                'description' => 'Chat specialists for OnlyFans accounts',
                'can_post_jobs' => false,
                'can_hire_talent' => false,
                'requires_kyc' => true,
                'required_fields' => json_encode([
                    'kyc_document',
                    'typing_speed',
                    'english_proficiency',
                    'experience_level',
                    'availability',
                    'portfolio'
                ]),
                'active' => true
            ],
            [
                'name' => 'manager',
                'display_name' => 'Account Manager',
                'description' => 'Account managers for OnlyFans creators',
                'can_post_jobs' => false,
                'can_hire_talent' => false,
                'requires_kyc' => true,
                'required_fields' => json_encode([
                    'kyc_document',
                    'experience_level',
                    'portfolio',
                    'management_tools',
                    'availability'
                ]),
                'active' => true
            ],
            [
                'name' => 'social_media_manager',
                'display_name' => 'Social Media Manager',
                'description' => 'Social media management specialists',
                'can_post_jobs' => false,
                'can_hire_talent' => false,
                'requires_kyc' => true,
                'required_fields' => json_encode([
                    'kyc_document',
                    'portfolio',
                    'platforms_expertise',
                    'experience_level',
                    'availability'
                ]),
                'active' => true
            ],
            [
                'name' => 'video_editor',
                'display_name' => 'Video Editor',
                'description' => 'Content video editing professionals',
                'can_post_jobs' => false,
                'can_hire_talent' => false,
                'requires_kyc' => true,
                'required_fields' => json_encode([
                    'kyc_document',
                    'portfolio',
                    'software_expertise',
                    'experience_level',
                    'turnaround_time'
                ]),
                'active' => true
            ],
            [
                'name' => 'graphic_designer',
                'display_name' => 'Graphic Designer',
                'description' => 'Visual content and branding specialists',
                'can_post_jobs' => false,
                'can_hire_talent' => false,
                'requires_kyc' => true,
                'required_fields' => json_encode([
                    'kyc_document',
                    'portfolio',
                    'design_specialties',
                    'software_expertise',
                    'experience_level'
                ]),
                'active' => true
            ],
            [
                'name' => 'marketing_expert',
                'display_name' => 'Marketing Expert',
                'description' => 'OnlyFans marketing and promotion experts',
                'can_post_jobs' => false,
                'can_hire_talent' => false,
                'requires_kyc' => true,
                'required_fields' => json_encode([
                    'kyc_document',
                    'portfolio',
                    'marketing_strategies',
                    'proven_results',
                    'experience_level'
                ]),
                'active' => true
            ]
        ];

        foreach ($userTypes as $userType) {
            UserType::create($userType);
        }
        
        $this->command->info('✅ Clean user types seeded successfully!');
        $this->command->info('🏢 3 Hiring entities created (can post jobs and hire talent)');
        $this->command->info('👤 6 Talent types created (can only apply to jobs)');
    }
}
