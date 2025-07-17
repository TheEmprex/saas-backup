<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserType;

class UserTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
                'active' => true
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
                'active' => true
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
                'active' => true
            ]
        ];

        foreach ($userTypes as $userType) {
            UserType::create($userType);
        }
    }
}
