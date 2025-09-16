<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\JobApplication;
use App\Models\JobPost;
use App\Models\KycVerification;
use App\Models\Message;
use App\Models\Rating;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MarketplaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create user types
        $userTypes = [
            ['name' => 'Content Creator', 'display_name' => 'Content Creator', 'description' => 'OnlyFans content creators looking for management services'],
            ['name' => 'Manager', 'display_name' => 'Manager', 'description' => 'Account managers for OnlyFans creators'],
            ['name' => 'Chatter', 'display_name' => 'Chatter', 'description' => 'Chat specialists for OnlyFans accounts'],
            ['name' => 'Agency', 'display_name' => 'Agency', 'description' => 'Full-service OnlyFans management agencies'],
            ['name' => 'Social Media Manager', 'display_name' => 'Social Media Manager', 'description' => 'Social media management specialists'],
            ['name' => 'Video Editor', 'display_name' => 'Video Editor', 'description' => 'Content video editing professionals'],
            ['name' => 'Graphic Designer', 'display_name' => 'Graphic Designer', 'description' => 'Visual content and branding specialists'],
            ['name' => 'Marketing Expert', 'display_name' => 'Marketing Expert', 'description' => 'OnlyFans marketing and promotion experts'],
        ];

        foreach ($userTypes as $type) {
            UserType::firstOrCreate(['name' => $type['name']], $type);
        }

        // Create test users
        $testUsers = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'user_type_id' => UserType::where('name', 'Manager')->first()->id,
                'role' => 'admin',
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'user_type_id' => UserType::where('name', 'Content Creator')->first()->id,
            ],
            [
                'name' => 'Mike Chen',
                'email' => 'mike@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'user_type_id' => UserType::where('name', 'Manager')->first()->id,
            ],
            [
                'name' => 'Emma Davis',
                'email' => 'emma@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'user_type_id' => UserType::where('name', 'Chatter')->first()->id,
            ],
            [
                'name' => 'ProManagement Agency',
                'email' => 'agency@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'user_type_id' => UserType::where('name', 'Agency')->first()->id,
            ],
            [
                'name' => 'Alex Rodriguez',
                'email' => 'alex@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'user_type_id' => UserType::where('name', 'Video Editor')->first()->id,
            ],
        ];

        foreach ($testUsers as $userData) {
            $role = $userData['role'] ?? 'registered';
            unset($userData['role']);

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            if ($role === 'admin') {
                $user->assignRole('admin');
            }

            // Create user profile
            $this->createUserProfile($user);

            // Create KYC verification for some users
            if (in_array($user->email, ['sarah@example.com', 'mike@example.com', 'agency@example.com'])) {
                $this->createKycVerification($user);
            }
        }

        // Create job posts
        $this->createJobPosts();

        // Create job applications
        $this->createJobApplications();

        // Create messages
        $this->createMessages();

        // Create ratings
        $this->createRatings();
    }

    private function createUserProfile(User $user): void
    {
        $profiles = [
            'sarah@example.com' => [
                'bio' => 'OnlyFans content creator with 50K+ followers. Looking for professional management to scale my business.',
                'availability_timezone' => 'America/Los_Angeles',
                'availability_hours' => json_encode(['9am-5pm PST']),
                'portfolio_links' => json_encode(['https://onlyfans.com/sarah']),
                'typing_speed_wpm' => 75,
                'english_proficiency_score' => 95,
                'experience_agencies' => json_encode(['Self-managed']),
                'traffic_sources' => json_encode(['Instagram', 'Twitter', 'TikTok']),
                'is_verified' => true,
                'is_active' => true,
            ],
            'mike@example.com' => [
                'bio' => 'Experienced OnlyFans manager with 5+ years in adult content management. Specializing in revenue optimization.',
                'availability_timezone' => 'America/New_York',
                'availability_hours' => json_encode(['24/7']),
                'portfolio_links' => json_encode(['https://mikechenmanagement.com']),
                'typing_speed_wpm' => 85,
                'english_proficiency_score' => 100,
                'experience_agencies' => json_encode(['Premium Management Co', 'Elite OF Agency']),
                'traffic_sources' => json_encode(['All major platforms']),
                'company_name' => 'Mike Chen Management',
                'company_description' => 'Professional OnlyFans management services',
                'is_verified' => true,
                'is_active' => true,
            ],
            'emma@example.com' => [
                'bio' => 'Professional OnlyFans chatter with excellent communication skills. Available 24/7.',
                'availability_timezone' => 'America/New_York',
                'availability_hours' => json_encode(['24/7']),
                'portfolio_links' => json_encode([]),
                'typing_speed_wpm' => 95,
                'english_proficiency_score' => 98,
                'experience_agencies' => json_encode(['Chat Pro Services', 'Night Shift Experts']),
                'traffic_sources' => json_encode(['Direct messaging', 'Live chat']),
                'is_verified' => true,
                'is_active' => true,
            ],
        ];

        $defaultProfile = [
            'bio' => 'Professional in the OnlyFans management industry.',
            'availability_timezone' => 'America/New_York',
            'availability_hours' => json_encode(['9am-5pm']),
            'portfolio_links' => json_encode([]),
            'typing_speed_wpm' => 60,
            'english_proficiency_score' => 85,
            'experience_agencies' => json_encode(['Various']),
            'traffic_sources' => json_encode(['Social Media']),
            'is_verified' => false,
            'is_active' => true,
        ];

        $profileData = $profiles[$user->email] ?? $defaultProfile;
        $profileData['user_id'] = $user->id;
        $profileData['user_type_id'] = $user->user_type_id;

        UserProfile::firstOrCreate(
            ['user_id' => $user->id],
            $profileData
        );
    }

    private function createKycVerification(User $user): void
    {
        $statuses = ['pending', 'approved', 'rejected'];
        $status = $statuses[array_rand($statuses)];

        KycVerification::firstOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => explode(' ', $user->name)[0],
                'last_name' => explode(' ', $user->name)[1] ?? 'User',
                'date_of_birth' => now()->subYears(random_int(20, 40)),
                'phone_number' => '+1'.random_int(1000000000, 9999999999),
                'address' => '123 Main Street',
                'city' => 'Los Angeles',
                'state' => 'California',
                'postal_code' => '90210',
                'country' => 'US',
                'id_document_type' => 'passport',
                'id_document_number' => 'P'.random_int(100000000, 999999999),
                'status' => $status,
                'submitted_at' => now()->subDays(random_int(1, 30)),
                'reviewed_at' => $status !== 'pending' ? now()->subDays(random_int(1, 7)) : null,
                'reviewed_by' => $status !== 'pending' ? User::where('email', 'admin@example.com')->first()->id : null,
                'rejection_reason' => $status === 'rejected' ? 'Document quality is not sufficient for verification.' : null,
            ]
        );
    }

    private function createJobPosts(): void
    {
        $jobPosts = [
            [
                'title' => 'Experienced OnlyFans Account Manager Needed',
                'description' => 'Looking for an experienced account manager to help grow my OnlyFans presence. Must have proven track record.',
                'market' => 'management',
                'rate_type' => 'commission',
                'commission_percentage' => 20,
                'max_applications' => 5,
                'contract_type' => 'full_time',
                'requirements' => 'Minimum 2 years experience managing OnlyFans accounts',
                'experience_level' => 'intermediate',
                'expected_response_time' => '2-4 hours',
                'hours_per_week' => 40,
                'timezone_preference' => 'America/Los_Angeles',
                'min_typing_speed' => 60,
                'min_english_proficiency' => 90,
                'user_email' => 'sarah@example.com',
            ],
            [
                'title' => 'OnlyFans Chat Support - Night Shift',
                'description' => 'Need reliable chat support for OnlyFans account during night hours (9PM-6AM EST).',
                'market' => 'chatting',
                'rate_type' => 'hourly',
                'hourly_rate' => 18,
                'max_applications' => 10,
                'contract_type' => 'part_time',
                'requirements' => 'Excellent English communication skills, available nights',
                'experience_level' => 'beginner',
                'expected_response_time' => '1 hour',
                'hours_per_week' => 35,
                'timezone_preference' => 'America/New_York',
                'working_hours' => json_encode(['9PM-6AM EST']),
                'min_typing_speed' => 80,
                'min_english_proficiency' => 95,
                'user_email' => 'sarah@example.com',
            ],
            [
                'title' => 'Video Editor for Adult Content',
                'description' => 'Looking for a skilled video editor to edit OnlyFans content. Must be comfortable with adult content.',
                'market' => 'content_creation',
                'rate_type' => 'fixed',
                'fixed_rate' => 150,
                'max_applications' => 8,
                'contract_type' => 'contract',
                'requirements' => 'Experience with Adobe Premiere/Final Cut Pro, portfolio required',
                'experience_level' => 'advanced',
                'expected_response_time' => '24-48 hours',
                'hours_per_week' => 20,
                'timezone_preference' => 'America/New_York',
                'user_email' => 'mike@example.com',
            ],
            [
                'title' => 'Social Media Manager for OnlyFans Promotion',
                'description' => 'Need someone to manage Twitter, Instagram, and TikTok accounts for OnlyFans promotion.',
                'market' => 'marketing',
                'rate_type' => 'hourly',
                'hourly_rate' => 25,
                'max_applications' => 15,
                'contract_type' => 'part_time',
                'requirements' => 'Social media marketing experience, knowledge of adult content policies',
                'experience_level' => 'intermediate',
                'expected_response_time' => '2-4 hours',
                'hours_per_week' => 30,
                'timezone_preference' => 'America/New_York',
                'required_traffic_sources' => json_encode(['Twitter', 'Instagram', 'TikTok']),
                'user_email' => 'agency@example.com',
            ],
            [
                'title' => 'OnlyFans Content Creator - Female Only',
                'description' => 'Established agency looking for new content creators to join our team. Full support provided.',
                'market' => 'content_creation',
                'rate_type' => 'commission',
                'commission_percentage' => 50,
                'max_applications' => 20,
                'contract_type' => 'full_time',
                'requirements' => 'Female, 18+, comfortable with adult content, reliable internet',
                'experience_level' => 'beginner',
                'expected_response_time' => '1-2 hours',
                'hours_per_week' => 20,
                'timezone_preference' => 'America/New_York',
                'user_email' => 'agency@example.com',
            ],
        ];

        foreach ($jobPosts as $jobData) {
            $userEmail = $jobData['user_email'];
            unset($jobData['user_email']);

            $user = User::where('email', $userEmail)->first();

            if ($user) {
                $jobData['user_id'] = $user->id;
                $jobData['status'] = 'active';
                $jobData['current_applications'] = 0;
                $jobData['views'] = random_int(5, 50);
                $jobData['start_date'] = now()->addDays(random_int(1, 14));
                $jobData['expires_at'] = now()->addDays(random_int(30, 90));
                $jobData['tags'] = json_encode(['OnlyFans', 'Remote', 'Adult Content']);

                JobPost::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'title' => $jobData['title'],
                    ],
                    $jobData
                );
            }
        }
    }

    private function createJobApplications(): void
    {
        $jobs = JobPost::all();
        $users = User::where('email', '!=', 'admin@example.com')->get();

        foreach ($jobs as $job) {
            $applicants = $users->where('id', '!=', $job->user_id)->random(random_int(1, 3));

            foreach ($applicants as $applicant) {
                // Calculate proposed rate based on job rate type
                $proposedRate = match ($job->rate_type) {
                    'hourly' => $job->hourly_rate + random_int(-5, 15),
                    'fixed' => $job->fixed_rate + random_int(-50, 100),
                    'commission' => $job->commission_percentage + random_int(-5, 5),
                    default => 50
                };

                JobApplication::firstOrCreate(
                    [
                        'job_post_id' => $job->id,
                        'user_id' => $applicant->id,
                    ],
                    [
                        'cover_letter' => 'I am very interested in this position and believe I have the skills and experience required.',
                        'proposed_rate' => $proposedRate,
                        'status' => ['pending', 'shortlisted', 'interviewed', 'hired', 'rejected'][array_rand(['pending', 'shortlisted', 'interviewed', 'hired', 'rejected'])],
                    ]
                );
            }

            // Update job application count
            $job->update(['current_applications' => $job->applications()->count()]);
        }
    }

    private function createMessages(): void
    {
        $users = User::where('email', '!=', 'admin@example.com')->get();

        for ($i = 0; $i < 20; $i++) {
            $sender = $users->random();
            $recipient = $users->where('id', '!=', $sender->id)->random();

            Message::create([
                'sender_id' => $sender->id,
                'recipient_id' => $recipient->id,
                'message_content' => 'Thank you for your application. I would like to discuss this opportunity further.',
                'message_type' => 'text',
                'is_read' => random_int(0, 1),
                'created_at' => now()->subDays(random_int(1, 30)),
            ]);
        }
    }

    private function createRatings(): void
    {
        $users = User::where('email', '!=', 'admin@example.com')->get();
        $jobPosts = JobPost::all();

        for ($i = 0; $i < 15; $i++) {
            $rater = $users->random();
            $rated = $users->where('id', '!=', $rater->id)->random();
            $jobPost = $jobPosts->random();

            Rating::firstOrCreate(
                [
                    'rater_id' => $rater->id,
                    'rated_id' => $rated->id,
                    'job_post_id' => $jobPost->id,
                ],
                [
                    'overall_rating' => random_int(3, 5),
                    'communication_rating' => random_int(3, 5),
                    'professionalism_rating' => random_int(3, 5),
                    'timeliness_rating' => random_int(3, 5),
                    'quality_rating' => random_int(3, 5),
                    'review_title' => 'Great Experience',
                    'review_content' => 'Great to work with! Professional and delivered quality work on time.',
                    'is_verified' => true,
                    'is_public' => true,
                ]
            );
        }
    }
}
