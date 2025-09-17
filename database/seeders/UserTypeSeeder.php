<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\UserType;
use Illuminate\Database\Seeder;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        UserType::create([
            'name' => 'chatter',
            'display_name' => 'Chatter',
            'description' => 'Individual workers providing chatting services to OnlyFans models and agencies.',
            'required_fields' => json_encode([
                'kyc_document',
                'typing_speed',
                'english_proficiency',
                'experience_level',
                'availability',
                'portfolio',
            ]),
            'requires_kyc' => true,
            'active' => true,
        ]);

        UserType::create([
            'name' => 'ofm_agency',
            'display_name' => 'OFM Agency',
            'description' => 'OnlyFans management agencies looking to hire chatters for their models.',
            'required_fields' => json_encode([
                'company_name',
                'business_email',
                'revenue_screenshots',
                'payment_proof',
                'team_size',
            ]),
            'requires_kyc' => false,
            'active' => true,
        ]);

        UserType::create([
            'name' => 'chatting_agency',
            'display_name' => 'Chatting Agency',
            'description' => 'Specialized agencies providing outsourced chatting services to OFM agencies.',
            'required_fields' => json_encode([
                'company_name',
                'team_list',
                'portfolio',
                'client_testimonials',
                'performance_metrics',
            ]),
            'requires_kyc' => false,
            'active' => true,
        ]);
    }
}
