<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\JobPost;
use App\Models\User;
use Illuminate\Database\Seeder;

class JobPostSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $user = User::first();

        if (! $user) {
            return;
        }

        JobPost::create([
            'user_id' => $user->id,
            'title' => 'English Market Chatter - Immediate Start',
            'description' => 'We are looking for an experienced English-speaking chatter to join our OnlyFans management team. You will be responsible for engaging with fans, building relationships, and maximizing revenue through strategic conversations.',
            'requirements' => 'Native English speaker\nMinimum 2 years experience\nFast typing skills (50+ WPM)\nExcellent communication skills\nKnowledge of OnlyFans platform',
            'benefits' => 'Flexible working hours\nCompetitive pay\nPerformance bonuses\nRemote work',
            'market' => 'english',
            'experience_level' => 'intermediate',
            'contract_type' => 'part_time',
            'rate_type' => 'hourly',
            'hourly_rate' => 15.00,
            'expected_hours_per_week' => 20,
            'duration_months' => 6,
            'min_typing_speed' => 50,
            'status' => 'active',
            'is_featured' => true,
            'max_applications' => 25,
            'current_applications' => 0,
        ]);

        JobPost::create([
            'user_id' => $user->id,
            'title' => 'Spanish Market Chatter - High Commission',
            'description' => 'Seeking native Spanish speakers for our expanding Latin American market. Perfect opportunity for experienced chatters looking for commission-based work with high earning potential.',
            'requirements' => 'Native Spanish speaker\nPrevious OnlyFans experience preferred\nAvailable during peak hours\nReliable internet connection',
            'benefits' => 'High commission rates\nFlexible schedule\nGrowth opportunities\nTeam support',
            'market' => 'spanish',
            'experience_level' => 'advanced',
            'contract_type' => 'contract',
            'rate_type' => 'commission',
            'commission_percentage' => 25.00,
            'expected_hours_per_week' => 30,
            'duration_months' => 12,
            'min_typing_speed' => 60,
            'status' => 'active',
            'is_urgent' => true,
            'max_applications' => 15,
            'current_applications' => 0,
        ]);

        JobPost::create([
            'user_id' => $user->id,
            'title' => 'Entry Level Chatter - Training Provided',
            'description' => 'Great opportunity for beginners to enter the OnlyFans management industry. We provide comprehensive training and ongoing support to help you succeed.',
            'requirements' => 'Good English communication\nWillingness to learn\nBasic computer skills\nPositive attitude',
            'benefits' => 'Paid training\nMentorship program\nCareer progression\nFixed monthly salary',
            'market' => 'english',
            'experience_level' => 'beginner',
            'contract_type' => 'full_time',
            'rate_type' => 'fixed',
            'fixed_rate' => 2500.00,
            'expected_hours_per_week' => 40,
            'duration_months' => 3,
            'min_typing_speed' => 30,
            'status' => 'active',
            'max_applications' => 50,
            'current_applications' => 0,
        ]);
    }
}
